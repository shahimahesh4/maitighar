<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PolicyPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::updateOrCreate(
            ['slug' => 'refund-return-policy'],
            [
                'title' => 'Refund & Return Policy',
                'content' => <<<'HTML'
<p>We want every order from Maitighar to reach you in good condition and match what you ordered. Please read the policy below before requesting a return or refund.</p>
<h2>Return eligibility</h2>
<p>You may request a return within 7 days of receiving your order. To be eligible, the item must be unused, unopened, in its original packaging, and accompanied by the order receipt or other proof of purchase.</p>
<h2>Items that cannot be returned</h2>
<ul>
<li>Opened, used, tested, or damaged products</li>
<li>Products without their original seal, packaging, labels, or accessories</li>
<li>Items purchased during clearance or marked as final sale</li>
<li>Gift cards, downloadable products, and completed services</li>
<li>Items damaged through misuse, poor storage, or failure to follow instructions</li>
</ul>
<h2>Damaged, defective, or incorrect orders</h2>
<p>If your order arrives damaged, defective, or different from what you ordered, contact us within 48 hours of delivery. Include your order number and clear photographs of the product, packaging, and delivery label so our team can review the issue.</p>
<h2>How to request a return</h2>
<ol>
<li>Contact our team before sending any item back.</li>
<li>Share your order number, reason for return, and supporting photographs when relevant.</li>
<li>Wait for return confirmation and instructions from our team.</li>
<li>Pack the approved item securely in its original packaging.</li>
</ol>
<p>Items returned without prior approval may not be accepted.</p>
<h2>Refunds</h2>
<p>After an approved return is received and inspected, we will notify you of the decision. Approved refunds are sent to the original payment method where possible. Bank and payment-provider processing times may vary.</p>
<p>Original delivery charges are non-refundable unless the order was incorrect, damaged, or defective when delivered. Return delivery costs are normally the customer's responsibility.</p>
<h2>Exchanges</h2>
<p>Where stock is available, an eligible item may be exchanged for the same product. If a replacement is unavailable, our team may offer store credit or an approved refund.</p>
<h2>Order cancellation</h2>
<p>Contact us as soon as possible if you need to cancel an order. An order can only be cancelled before it has been processed or dispatched.</p>
<h2>Contact us</h2>
<p>For return or refund assistance, email <strong>maitigharaustralia@gmail.com</strong> or call <strong>+61 432 685 546</strong>. Please include your order number in every request.</p>
HTML,
                'thumbnail_image' => 'images/kba/about-us-academy.png',
                'banner_image' => 'images/kba/about-us-academy.png',
                'meta_title' => 'Refund & Return Policy | Maitighar',
                'meta_description' => 'Read the refund, return, exchange, damaged-item, and order-cancellation policy for Maitighar online purchases.',
                'meta_keywords' => 'refund policy, return policy, exchanges, order cancellation, Maitighar, Nepal',
                'status' => 'published',
                'sort_order' => 20,
            ]
        );

        foreach ([
            ['How long do I have to request a return?', 'Return requests should be made within 7 days of receiving the order.'],
            ['Can I return an opened personal-care product?', 'For hygiene and safety reasons, opened, used, or tested personal-care products cannot normally be returned.'],
            ['What should I do if my order arrives damaged?', 'Contact us within 48 hours and include your order number with clear photographs of the item, packaging, and delivery label.'],
        ] as $index => [$question, $answer]) {
            $page->faqs()->updateOrCreate(
                ['question' => $question],
                ['answer' => '<p>'.$answer.'</p>', 'status' => 'published', 'sort_order' => $index + 1]
            );
        }
    }
}
