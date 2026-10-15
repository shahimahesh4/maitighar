<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $rows = DB::table('settings')->get();

        foreach ($rows as $row) {
            $value = $row->value;

            // If value is already encrypted, skip. Crypt::decryptString will throw for plaintext.
            try {
                Crypt::decryptString($value);
                continue;
            } catch (\Throwable $e) {
                // Not encrypted — encrypt and update
                $toStore = is_string($value) ? $value : json_encode($value);
                $encrypted = Crypt::encryptString($toStore);
                DB::table('settings')->where('id', $row->id)->update(['value' => $encrypted]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank: reversing would expose secrets as plaintext.
    }
};
