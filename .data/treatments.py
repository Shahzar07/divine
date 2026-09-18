# -*- coding: utf-8 -*-
"""
The studio's treatment menu — the single source of truth.

Both the static build and the WordPress theme are generated from this, so the
services page, the home page carousel and the enquiry dropdown can never drift
apart. Edit here, then run tools/generate.py.
"""

# group key -> (eyebrow, heading, standfirst)
GROUPS = [
    ('skin', 'Facials & skin', 'Skin, properly<br><em>looked after.</em>',
     'Six facials, from a straightforward cleanse-and-glow to advanced resurfacing. '
     'Every one starts with a look at your skin on the day, not a fixed script.'),
    ('body', 'Massage & body', 'Tension out.<br><em>Ease back in.</em>',
     'Five massages and body treatments, worked at the pressure that suits you — '
     'and a room quiet enough to properly switch off.'),
    ('nails', 'Nails', 'Shaped to you.<br><em>Sealed to last.</em>',
     'Prepped properly, balanced to your own nail and finished so it wears '
     'instead of lifting.'),
    ('glam', 'Makeup & finishing', 'Skin first.<br><em>Then the drama.</em>',
     'Matched in daylight, built to last the whole day and to photograph true.'),
]

# slug, name, group, image, alt, description, benefits[], featured-on-home
TREATMENTS = [
    # ---------------------------------------------------------------- skin --
    ('basic-facial', 'Basic Facial', 'skin', 'svc-facials.jpg',
     'A calming treatment mask during a facial',
     'A proper double cleanse, gentle exfoliation, a mask chosen for your skin that '
     'day and a relaxing facial massage. The reset appointment — good for congestion, '
     'dullness or simply a skin that has been ignored for a while.',
     ['Double cleanse without stripping the skin',
      'Exfoliation matched to your skin type',
      'Facial massage to lift and de-puff',
      'Aftercare advice, never a product upsell'], True),

    ('luxury-facial', 'Luxury Facial', 'skin', 'band-facials.jpg',
     'A rich treatment mask applied during a luxury facial',
     'Everything in the basic facial, taken slower and further: a double mask, '
     'extended lymphatic massage through the face, neck and décolletage, an eye '
     'treatment and the cooling globes to finish.',
     ['A longer, unhurried appointment',
      'Double mask and an eye treatment',
      'Neck and décolletage included',
      'Cooling globes to settle redness'], True),

    ('dermaplaning', 'Dermaplaning', 'skin', 'svc-dermaplaning.jpg',
     'A gloved practitioner working a dermaplaning blade across the cheek',
     'A sterile blade is drawn across the skin at an angle to lift away dead cells '
     'and the fine vellus hair that traps them. Skin is immediately smoother, makeup '
     'sits flat, and everything applied afterwards absorbs better.',
     ['Instantly smoother, brighter skin',
      'Makeup sits flat with no peach fuzz',
      'Products absorb far better afterwards',
      'No downtime — walk out and carry on'], True),

    ('microneedling', 'Microneedling', 'skin', 'work-microneedling.jpg',
     'Microneedling being carried out at the studio',
     'Fine needles create controlled micro-channels that prompt the skin to rebuild '
     'its own collagen. The treatment to book for texture, scarring, large pores and '
     'fine lines — worked in a course rather than as a one-off.',
     ['Targets scarring, texture and fine lines',
      'Stimulates your own collagen',
      'Best results over a course of sessions',
      'Expect redness for 24–48 hours'], True),

    ('korean-glass-skin', 'Korean Glass Skin', 'skin', 'svc-glass-skin.jpg',
     'Dewy, luminous skin after a glass skin facial',
     'The layered Korean routine done properly in one appointment: deep cleanse, '
     'gentle resurfacing, essence, ampoule and a hydrating mask, built up in thin '
     'layers until the skin is genuinely reflective rather than shiny.',
     ['Deep, layered hydration',
      'A luminous, reflective finish',
      'Refines pores and evens tone',
      'Beautiful before an event'], True),

    ('microdermabrasion', 'Microdermabrasion Facial', 'skin', 'svc-microdermabrasion.jpg',
     'A microdermabrasion handpiece worked across the cheek',
     'A handheld device resurfaces the top layer of skin and vacuums away the debris '
     'in the same pass. Effective on dull, congested or uneven skin, and a good first '
     'step before starting a microneedling course.',
     ['Resurfaces dull, congested skin',
      'Clears blackheads and debris',
      'Softens uneven texture and tone',
      'Comfortable, with no downtime'], False),

    # ---------------------------------------------------------------- body --
    ('swedish-massage', 'Swedish Massage', 'body', 'svc-swedish.jpg',
     'Warm oil worked in long strokes across the back',
     'The classic full-body massage: long, flowing strokes with warm oil at a medium '
     'pressure. The one to book when you want to unwind rather than be worked on.',
     ['Eases everyday stress and tension',
      'Improves circulation',
      'Encourages deeper, easier sleep',
      'Medium pressure — relaxing, not intense'], True),

    ('deep-tissue-massage', 'Deep Tissue Massage', 'body', 'svc-deep-tissue.jpg',
     'Firm massage work through the shoulder and upper back',
     'Slower and firmer, working into the deeper layers of muscle and the knots that '
     'a relaxing massage will not shift. For desk shoulders, training soreness and '
     'long-standing tightness.',
     ['Releases stubborn knots and adhesions',
      'Built for desk and training tension',
      'Pressure agreed with you as we go',
      'Pairs well with cupping'], True),

    ('lymphatic-drainage', 'Lymphatic Drainage Massage', 'body', 'svc-lymphatic.jpg',
     'Light, rhythmic lymphatic drainage work along the neck and shoulder',
     'Very light, rhythmic strokes that follow the lymphatic pathways to move retained '
     'fluid towards the nodes. Popular for puffiness, bloating, sluggishness and as '
     'post-operative aftercare once you have been cleared.',
     ['Reduces puffiness and fluid retention',
      'Very light pressure — nothing forceful',
      'Supports post-operative recovery',
      'Leaves you lighter and less sluggish'], True),

    ('pregnancy-massage', 'Pregnancy Massage', 'body', 'svc-pregnancy.jpg',
     'A pregnant client resting comfortably during a prenatal massage',
     'Side-lying and fully supported with cushions, at a gentle pressure and avoiding '
     'the areas that are not appropriate in pregnancy. Available from the second '
     'trimester onwards.',
     ['Side-lying and cushion-supported',
      'Eases lower back and hip ache',
      'Second trimester onwards',
      'Reduces swelling in legs and feet'], True),

    ('body-contouring', 'Manual Body Contouring', 'body', 'work-contouring-waist.jpg',
     'The waist and abdomen after a manual body contouring session at the studio',
     'A hands-on, non-invasive sculpting massage that combines deep manual work with '
     'lymphatic drainage to smooth, define and reduce fluid around the waist, abdomen '
     'and thighs. Results build over a course.',
     ['Defines the waist and smooths the abdomen',
      'Entirely manual — no machines, no needles',
      'Combines sculpting with lymphatic drainage',
      'Visible from the first session, best over a course'], True),

    ('cupping', 'Cupping Therapy', 'body', 'band-cupping.jpg',
     'A cupping therapy session in a calm treatment room',
     'Dry cupping places suction cups along the back and shoulders to lift the tissue '
     'rather than press into it. The round marks it leaves are normal and usually fade '
     'within a few days.',
     ['Targets stubborn upper-back tightness',
      'A deep, different sensation to massage',
      'Pairs naturally with a back massage',
      'A favourite for post-training recovery'], False),

    # --------------------------------------------------------------- nails --
    ('gel-nails', 'Gel Nails', 'nails', 'svc-gel-nails.jpg',
     'Deep burgundy gel nails, freshly finished',
     'Careful prep, precise shaping and a smooth, glass-like gel finish in the colour '
     'you have been saving a photo of. Sealed properly so it wears without lifting or '
     'chipping.',
     ['High-shine finish that lasts two to three weeks',
      'Shaped to suit your own nail bed',
      'Adds strength to natural nails',
      'Touch-dry the moment you leave'], True),

    ('extensions', 'Nail Extensions', 'nails', 'svc-extensions.jpg',
     'Long black nail extensions worn with gold rings',
     'Sculpted acrylic or builder-gel extensions in your preferred length and shape — '
     'almond, square, coffin or stiletto — balanced to the width of your natural nail.',
     ['Any length and shape you like',
      'Strong enough for everyday wear',
      'Infills available to keep the set going',
      'Safe removal, never prised off'], False),

    ('mani-pedi', 'Manicure & Pedicure', 'nails', 'svc-mani-pedi.jpg',
     'A freshly finished pedicure',
     'Classic shaping, cuticle work, buffing and a flawless polish. The pedicure adds '
     'a soak, hard-skin care and a warm massage.',
     ['Tidy, healthy nails and cuticles',
      'Hard-skin and callus care',
      'Hydrating hand and foot massage',
      'Regular or long-wear gel finish'], False),

    # ---------------------------------------------------------------- glam --
    ('makeup', 'Makeup & Glam', 'glam', 'svc-makeup.jpg',
     'A clean, luminous glam makeup finish',
     'Skin prepped and primed first, then a base matched in natural light so it never '
     'turns grey in photographs. Day looks, occasions, bridal and bridal parties.',
     ['Shade matched in daylight',
      'Long-wear formulas for a full day',
      'Lashes chosen to suit your eye shape',
      'Group and bridal-party timings available'], True),

    ('brows', 'Brow & Lash Finish', 'glam', 'svc-brows.jpg',
     'Defined brows and lashes, close up',
     'Brows mapped to your features, shaped, tidied and tinted if you want more depth — '
     'with lashes chosen to match.',
     ['Mapped to your face, not a template',
      'Defines the eyes without heavy makeup',
      'Tint for extra depth where you want it',
      'Pairs well with a makeup or facial booking'], False),
]

GROUP_LABEL = {k: v for k, v, _h, _s in [(g[0], g[1], g[2], g[3]) for g in GROUPS]}
