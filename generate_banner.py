import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter
from collections import deque

def generate_banner():
    # 1. Output dimensions
    W = 1920
    H = 800

    # Create base image with soft pastel studio gradient (periwinkle/soft blue-gray like reference)
    # Reference background: top-left (235, 240, 252) to bottom-right (221, 231, 247)
    base = Image.new('RGBA', (W, H))
    draw = ImageDraw.Draw(base)

    for y in range(H):
        t = y / float(H)
        # Gradient interpolation
        r = int(235 * (1 - t * 0.7) + 221 * (t * 0.7))
        g = int(240 * (1 - t * 0.7) + 231 * (t * 0.7))
        b = int(252 * (1 - t * 0.7) + 247 * (t * 0.7))
        draw.line([(0, y), (W, y)], fill=(r, g, b, 255))

    # Add soft diagonal variation across X
    diag_overlay = Image.new('RGBA', (W, H), (0, 0, 0, 0))
    d_draw = ImageDraw.Draw(diag_overlay)
    for x in range(W):
        t = x / float(W)
        alpha = int(25 * (1 - t))
        d_draw.line([(x, 0), (x, H)], fill=(255, 255, 255, alpha))
    base = Image.alpha_composite(base, diag_overlay)

    # Decorative Circles in background (matching reference)
    decor = Image.new('RGBA', (W, H), (0, 0, 0, 0))
    dec_draw = ImageDraw.Draw(decor)

    # Large circle behind mug: center (1380, 390), radius 320
    circle_color = (212, 225, 245, 210)
    dec_draw.ellipse([1380 - 320, 390 - 320, 1380 + 320, 390 + 320], fill=circle_color)

    # Floating circles
    dec_draw.ellipse([1670 - 65, 145 - 65, 1670 + 65, 145 + 65], fill=(216, 227, 247, 180))
    dec_draw.ellipse([1690 - 45, 750 - 45, 1690 + 45, 750 + 45], fill=(216, 227, 247, 180))
    dec_draw.ellipse([980 - 18, 240 - 18, 980 + 18, 240 + 18], fill=(208, 220, 244, 200))
    dec_draw.ellipse([1120 - 14, 510 - 14, 1120 + 14, 510 + 14], fill=(208, 220, 244, 200))
    dec_draw.ellipse([1830 - 30, 480 - 30, 1830 + 30, 480 + 30], fill=(216, 227, 247, 140))

    base = Image.alpha_composite(base, decor)

    # 2. Extract and prepare Mug Image
    mug_src_path = 'C:/Users/mahes/.gemini/antigravity/brain/bda630ae-261f-405a-9638-3da44bf13adf/.user_uploaded/media_1792601817576_e272794f.png'
    raw_mug = Image.open(mug_src_path).convert('RGBA')
    mw, mh = raw_mug.size

    # Flood fill background removal from corners and inside handle (740, 500)
    visited = bytearray(mw * mh)
    q = deque([(0, 0), (mw - 1, 0), (0, mh - 1), (mw - 1, mh - 1), (740, 500)])
    mpix = raw_mug.load()
    for x, y in list(q):
        visited[y * mw + x] = 1

    while q:
        cx, cy = q.popleft()
        for dx, dy in ((-1,0), (1,0), (0,-1), (0,1)):
            nx, ny = cx + dx, cy + dy
            if 0 <= nx < mw and 0 <= ny < mh and not visited[ny * mw + nx]:
                r, g, b, a = mpix[nx, ny]
                if r > 240 and g > 240 and b > 240:
                    visited[ny * mw + nx] = 1
                    q.append((nx, ny))

    alpha_mask = Image.new('L', (mw, mh), 255)
    amask_pix = alpha_mask.load()
    for y in range(mh):
        for x in range(mw):
            if visited[y * mw + x]:
                amask_pix[x, y] = 0

    # Gentle feathering
    alpha_mask = alpha_mask.filter(ImageFilter.GaussianBlur(0.8))
    raw_mug.putalpha(alpha_mask)
    bbox = raw_mug.getbbox()
    mug_cutout = raw_mug.crop(bbox)

    # Scale mug so height is ~590px
    target_mug_h = 590
    target_mug_w = int(mug_cutout.width * (target_mug_h / mug_cutout.height))
    mug_scaled = mug_cutout.resize((target_mug_w, target_mug_h), Image.Resampling.LANCZOS)

    # Dynamic tilt (-11 degrees counter-clockwise like in reference)
    mug_tilted = mug_scaled.rotate(-11, resample=Image.Resampling.BICUBIC, expand=True)

    # Realistic contact shadow underneath mug
    shadow_layer = Image.new('RGBA', (W, H), (0, 0, 0, 0))
    s_draw = ImageDraw.Draw(shadow_layer)
    # Ellipse shadow
    sx1, sy1 = 1120, 685
    sx2, sy2 = 1620, 755
    s_draw.ellipse([sx1, sy1, sx2, sy2], fill=(40, 60, 95, 110))
    # Inner denser shadow
    s_draw.ellipse([sx1 + 70, sy1 + 10, sx2 - 70, sy2 - 10], fill=(25, 40, 70, 140))
    shadow_layer = shadow_layer.filter(ImageFilter.GaussianBlur(24))
    base = Image.alpha_composite(base, shadow_layer)

    # Paste mug onto base
    # Position mug on the right
    mug_x = 1060
    mug_y = 125
    base.alpha_composite(mug_tilted, (mug_x, mug_y))

    # 3. Create Starburst Badge ("17% OFF") matching reference
    def make_starburst(radius, inner_radius, points=20, color=(255, 122, 0, 255)):
        size = int(radius * 2.2)
        cx, cy = size / 2, size / 2
        img = Image.new('RGBA', (size, size), (0, 0, 0, 0))
        d = ImageDraw.Draw(img)
        pts = []
        step = math.pi / points
        for i in range(points * 2):
            r = radius if i % 2 == 0 else inner_radius
            a = i * step
            pts.append((cx + r * math.cos(a), cy + r * math.sin(a)))
        d.polygon(pts, fill=color)
        return img

    starburst = make_starburst(72, 60, 20, (255, 122, 0, 255))
    sb_draw = ImageDraw.Draw(starburst)
    font_pct = ImageFont.truetype('C:/Windows/Fonts/bahnschrift.ttf', 38)
    font_off = ImageFont.truetype('C:/Windows/Fonts/bahnschrift.ttf', 24)

    # Center text inside starburst
    sw, sh = starburst.size
    # 17%
    bbox_pct = sb_draw.textbbox((0, 0), "17%", font=font_pct)
    tw_pct = bbox_pct[2] - bbox_pct[0]
    sb_draw.text(((sw - tw_pct) / 2, sh / 2 - 34), "17%", font=font_pct, fill=(255, 255, 255, 255))
    # OFF
    bbox_off = sb_draw.textbbox((0, 0), "OFF", font=font_off)
    tw_off = bbox_off[2] - bbox_off[0]
    sb_draw.text(((sw - tw_off) / 2, sh / 2 + 6), "OFF", font=font_off, fill=(255, 255, 255, 255))

    # Soft shadow for starburst
    sb_shadow = Image.new('RGBA', (sw + 20, sh + 20), (0, 0, 0, 0))
    sb_sdraw = ImageDraw.Draw(sb_shadow)
    sb_sdraw.ellipse([10, 10, sw + 10, sh + 10], fill=(255, 122, 0, 80))
    sb_shadow = sb_shadow.filter(ImageFilter.GaussianBlur(10))

    base.alpha_composite(sb_shadow, (960, 440))
    base.alpha_composite(starburst, (970, 450))

    # Small secondary starburst dot nearby (like reference)
    starburst_sm = make_starburst(18, 14, 12, (255, 122, 0, 255))
    base.alpha_composite(starburst_sm, (1095, 560))

    # 4. Draw Left Content Elements (Typography, Badges, Button)
    overlay = Image.new('RGBA', (W, H), (0, 0, 0, 0))
    draw_ov = ImageDraw.Draw(overlay)

    font_badge = ImageFont.truetype('C:/Windows/Fonts/segoeuib.ttf', 20)
    font_h1 = ImageFont.truetype('C:/Windows/Fonts/segoeuib.ttf', 68)
    font_sub = ImageFont.truetype('C:/Windows/Fonts/segoeuib.ttf', 22)
    font_price_sale = ImageFont.truetype('C:/Windows/Fonts/segoeuib.ttf', 42)
    font_price_reg = ImageFont.truetype('C:/Windows/Fonts/segoeuib.ttf', 28)
    font_btn = ImageFont.truetype('C:/Windows/Fonts/segoeuib.ttf', 21)

    # 4.1 Pill Badge: "100% Best Product"
    badge_x = 210
    badge_y = 150
    badge_w = 220
    badge_h = 44
    draw_ov.rounded_rectangle([badge_x, badge_y, badge_x + badge_w, badge_y + badge_h], radius=22, fill=(83, 103, 255, 255))
    bbox_b = draw_ov.textbbox((0, 0), "100% Best Product", font=font_badge)
    tb_w = bbox_b[2] - bbox_b[0]
    tb_h = bbox_b[3] - bbox_b[1]
    draw_ov.text((badge_x + (badge_w - tb_w) / 2, badge_y + (badge_h - tb_h) / 2 - 2), "100% Best Product", font=font_badge, fill=(255, 255, 255, 255))

    # 4.2 Headline:
    # "Bamboo Coffee Mug"
    # "with Dual Opening"
    title_color = (22, 31, 56, 255) # Deep navy slate
    draw_ov.text((210, 225), "Bamboo Coffee Mug", font=font_h1, fill=title_color)
    draw_ov.text((210, 312), "with Dual Opening", font=font_h1, fill=title_color)

    # Orange underline ribbon under "with Dual Opening" (like reference)
    under_x = 210
    under_y = 405
    under_w = 540
    under_h = 10
    draw_ov.rounded_rectangle([under_x, under_y, under_x + under_w, under_y + under_h], radius=5, fill=(255, 122, 0, 255))

    # 4.3 Sub-headline: "Custom Mug Printing" (or "Custom Laser Engraved • 100% Eco-Friendly")
    draw_ov.text((210, 445), "Custom Mug Laser Engraved", font=font_sub, fill=(80, 92, 115, 255))

    # 4.4 Price: "$ 24.00 / $29.00"
    price_y = 495
    draw_ov.text((210, price_y), "$ 24.00", font=font_price_sale, fill=(22, 31, 56, 255))
    reg_x = 385
    draw_ov.text((reg_x, price_y + 11), "/  $29.00", font=font_price_reg, fill=(140, 155, 180, 255))
    # Strikethrough line over $29.00
    st_start = reg_x + 30
    st_end = st_start + 85
    draw_ov.line([(st_start, price_y + 27), (st_end, price_y + 27)], fill=(140, 155, 180, 255), width=2)

    # 4.5 Button ("Shop Now" with Orange Arrow Circle)
    btn_x = 210
    btn_y = 575
    btn_w = 215
    btn_h = 62

    # Button shadow
    btn_shadow = Image.new('RGBA', (W, H), (0, 0, 0, 0))
    b_sdraw = ImageDraw.Draw(btn_shadow)
    b_sdraw.rounded_rectangle([btn_x + 2, btn_y + 4, btn_x + btn_w + 2, btn_y + btn_h + 6], radius=31, fill=(20, 35, 70, 45))
    btn_shadow = btn_shadow.filter(ImageFilter.GaussianBlur(10))
    base = Image.alpha_composite(base, btn_shadow)

    # Button body
    draw_ov.rounded_rectangle([btn_x, btn_y, btn_x + btn_w, btn_y + btn_h], radius=31, fill=(255, 255, 255, 255))
    draw_ov.text((btn_x + 30, btn_y + 18), "Shop Now", font=font_btn, fill=(22, 31, 56, 255))

    # Arrow circle
    circle_cx = btn_x + btn_w - 32
    circle_cy = btn_y + btn_h / 2
    circle_r = 23
    draw_ov.ellipse([circle_cx - circle_r, circle_cy - circle_r, circle_cx + circle_r, circle_cy + circle_r], fill=(255, 122, 0, 255))

    # Draw arrow icon ↗
    arrow_color = (255, 255, 255, 255)
    ax = circle_cx
    ay = circle_cy
    draw_ov.line([(ax - 7, ay + 7), (ax + 6, ay - 6)], fill=arrow_color, width=3)
    draw_ov.line([(ax - 2, ay - 6), (ax + 6, ay - 6)], fill=arrow_color, width=3)
    draw_ov.line([(ax + 6, ay - 6), (ax + 6, ay + 2)], fill=arrow_color, width=3)

    # 5. Bottom Pagination Dots (matching reference)
    dot_y = 755
    dot_cx = 960
    # Active orange pill
    draw_ov.rounded_rectangle([dot_cx - 24, dot_y - 4, dot_cx + 8, dot_y + 4], radius=4, fill=(255, 122, 0, 255))
    # Inactive dot 1
    draw_ov.ellipse([dot_cx + 16, dot_y - 4, dot_cx + 24, dot_y + 4], fill=(22, 31, 56, 255))
    # Inactive dot 2
    draw_ov.ellipse([dot_cx + 32, dot_y - 4, dot_cx + 40, dot_y + 4], fill=(22, 31, 56, 255))

    # Composite overlay
    final_img = Image.alpha_composite(base, overlay)

    # Save PNG and JPG in public/images/banners/ and public/storage/sliders/
    os.makedirs('public/images/banners', exist_ok=True)
    os.makedirs('public/storage/sliders', exist_ok=True)

    png_path = 'public/images/banners/bamboo_coffee_mug_banner_1920x800.png'
    jpg_path = 'public/images/banners/bamboo_coffee_mug_banner_1920x800.jpg'
    slider_path = 'public/storage/sliders/bamboo_coffee_mug_banner.png'

    final_img.save(png_path, 'PNG', optimize=True)
    final_img.convert('RGB').save(jpg_path, 'JPEG', quality=95)
    final_img.save(slider_path, 'PNG', optimize=True)

    print(f"Banner generated successfully: {png_path} ({W}x{H})")

if __name__ == '__main__':
    generate_banner()
