<?php
/**
 * The studio's treatment menu.
 *
 * GENERATED FILE — do not edit by hand.
 * Source: .data/treatments.py   Regenerate: python3 tools/generate.py
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The four groups the menu is organised into.
 *
 * @return array<int,array<string,string>>
 */
function divine_treatment_groups(): array {
	return array(
		array(
			'key'        => 'skin',
			'label'      => 'Facials & skin',
			'heading'    => 'Skin, properly<br><em>looked after.</em>',
			'standfirst' => 'Six facials, from a straightforward cleanse-and-glow to advanced resurfacing. Every one starts with a look at your skin on the day, not a fixed script.',
		),
		array(
			'key'        => 'body',
			'label'      => 'Massage & body',
			'heading'    => 'Tension out.<br><em>Ease back in.</em>',
			'standfirst' => 'Five massages and body treatments, worked at the pressure that suits you — and a room quiet enough to properly switch off.',
		),
		array(
			'key'        => 'nails',
			'label'      => 'Nails',
			'heading'    => 'Shaped to you.<br><em>Sealed to last.</em>',
			'standfirst' => 'Prepped properly, balanced to your own nail and finished so it wears instead of lifting.',
		),
		array(
			'key'        => 'glam',
			'label'      => 'Makeup & finishing',
			'heading'    => 'Skin first.<br><em>Then the drama.</em>',
			'standfirst' => 'Matched in daylight, built to last the whole day and to photograph true.',
		),
	);
}

/**
 * Every treatment, in menu order.
 *
 * @return array<int,array<string,mixed>>
 */
function divine_treatments(): array {
	return array(
		array(
			'slug'     => 'basic-facial',
			'name'     => 'Basic Facial',
			'group'    => 'skin',
			'image'    => 'svc-facials.jpg',
			'alt'      => 'A calming treatment mask during a facial',
			'text'     => 'A proper double cleanse, gentle exfoliation, a mask chosen for your skin that day and a relaxing facial massage. The reset appointment — good for congestion, dullness or simply a skin that has been ignored for a while.',
			'benefits' => 'Double cleanse without stripping the skin
Exfoliation matched to your skin type
Facial massage to lift and de-puff
Aftercare advice, never a product upsell',
			'featured' => true,
		),
		array(
			'slug'     => 'luxury-facial',
			'name'     => 'Luxury Facial',
			'group'    => 'skin',
			'image'    => 'band-facials.jpg',
			'alt'      => 'A rich treatment mask applied during a luxury facial',
			'text'     => 'Everything in the basic facial, taken slower and further: a double mask, extended lymphatic massage through the face, neck and décolletage, an eye treatment and the cooling globes to finish.',
			'benefits' => 'A longer, unhurried appointment
Double mask and an eye treatment
Neck and décolletage included
Cooling globes to settle redness',
			'featured' => true,
		),
		array(
			'slug'     => 'dermaplaning',
			'name'     => 'Dermaplaning',
			'group'    => 'skin',
			'image'    => 'svc-dermaplaning.jpg',
			'alt'      => 'A gloved practitioner working a dermaplaning blade across the cheek',
			'text'     => 'A sterile blade is drawn across the skin at an angle to lift away dead cells and the fine vellus hair that traps them. Skin is immediately smoother, makeup sits flat, and everything applied afterwards absorbs better.',
			'benefits' => 'Instantly smoother, brighter skin
Makeup sits flat with no peach fuzz
Products absorb far better afterwards
No downtime — walk out and carry on',
			'featured' => true,
		),
		array(
			'slug'     => 'microneedling',
			'name'     => 'Microneedling',
			'group'    => 'skin',
			'image'    => 'work-microneedling.jpg',
			'alt'      => 'Microneedling being carried out at the studio',
			'text'     => 'Fine needles create controlled micro-channels that prompt the skin to rebuild its own collagen. The treatment to book for texture, scarring, large pores and fine lines — worked in a course rather than as a one-off.',
			'benefits' => 'Targets scarring, texture and fine lines
Stimulates your own collagen
Best results over a course of sessions
Expect redness for 24–48 hours',
			'featured' => true,
		),
		array(
			'slug'     => 'korean-glass-skin',
			'name'     => 'Korean Glass Skin',
			'group'    => 'skin',
			'image'    => 'svc-glass-skin.jpg',
			'alt'      => 'Dewy, luminous skin after a glass skin facial',
			'text'     => 'The layered Korean routine done properly in one appointment: deep cleanse, gentle resurfacing, essence, ampoule and a hydrating mask, built up in thin layers until the skin is genuinely reflective rather than shiny.',
			'benefits' => 'Deep, layered hydration
A luminous, reflective finish
Refines pores and evens tone
Beautiful before an event',
			'featured' => true,
		),
		array(
			'slug'     => 'microdermabrasion',
			'name'     => 'Microdermabrasion Facial',
			'group'    => 'skin',
			'image'    => 'svc-microdermabrasion.jpg',
			'alt'      => 'A microdermabrasion handpiece worked across the cheek',
			'text'     => 'A handheld device resurfaces the top layer of skin and vacuums away the debris in the same pass. Effective on dull, congested or uneven skin, and a good first step before starting a microneedling course.',
			'benefits' => 'Resurfaces dull, congested skin
Clears blackheads and debris
Softens uneven texture and tone
Comfortable, with no downtime',
			'featured' => false,
		),
		array(
			'slug'     => 'swedish-massage',
			'name'     => 'Swedish Massage',
			'group'    => 'body',
			'image'    => 'svc-swedish.jpg',
			'alt'      => 'Warm oil worked in long strokes across the back',
			'text'     => 'The classic full-body massage: long, flowing strokes with warm oil at a medium pressure. The one to book when you want to unwind rather than be worked on.',
			'benefits' => 'Eases everyday stress and tension
Improves circulation
Encourages deeper, easier sleep
Medium pressure — relaxing, not intense',
			'featured' => true,
		),
		array(
			'slug'     => 'deep-tissue-massage',
			'name'     => 'Deep Tissue Massage',
			'group'    => 'body',
			'image'    => 'svc-deep-tissue.jpg',
			'alt'      => 'Firm massage work through the shoulder and upper back',
			'text'     => 'Slower and firmer, working into the deeper layers of muscle and the knots that a relaxing massage will not shift. For desk shoulders, training soreness and long-standing tightness.',
			'benefits' => 'Releases stubborn knots and adhesions
Built for desk and training tension
Pressure agreed with you as we go
Pairs well with cupping',
			'featured' => true,
		),
		array(
			'slug'     => 'lymphatic-drainage',
			'name'     => 'Lymphatic Drainage Massage',
			'group'    => 'body',
			'image'    => 'svc-lymphatic.jpg',
			'alt'      => 'Light, rhythmic lymphatic drainage work along the neck and shoulder',
			'text'     => 'Very light, rhythmic strokes that follow the lymphatic pathways to move retained fluid towards the nodes. Popular for puffiness, bloating, sluggishness and as post-operative aftercare once you have been cleared.',
			'benefits' => 'Reduces puffiness and fluid retention
Very light pressure — nothing forceful
Supports post-operative recovery
Leaves you lighter and less sluggish',
			'featured' => true,
		),
		array(
			'slug'     => 'pregnancy-massage',
			'name'     => 'Pregnancy Massage',
			'group'    => 'body',
			'image'    => 'svc-pregnancy.jpg',
			'alt'      => 'A pregnant client resting comfortably during a prenatal massage',
			'text'     => 'Side-lying and fully supported with cushions, at a gentle pressure and avoiding the areas that are not appropriate in pregnancy. Available from the second trimester onwards.',
			'benefits' => 'Side-lying and cushion-supported
Eases lower back and hip ache
Second trimester onwards
Reduces swelling in legs and feet',
			'featured' => true,
		),
		array(
			'slug'     => 'body-contouring',
			'name'     => 'Manual Body Contouring',
			'group'    => 'body',
			'image'    => 'work-contouring-waist.jpg',
			'alt'      => 'The waist and abdomen after a manual body contouring session at the studio',
			'text'     => 'A hands-on, non-invasive sculpting massage that combines deep manual work with lymphatic drainage to smooth, define and reduce fluid around the waist, abdomen and thighs. Results build over a course.',
			'benefits' => 'Defines the waist and smooths the abdomen
Entirely manual — no machines, no needles
Combines sculpting with lymphatic drainage
Visible from the first session, best over a course',
			'featured' => true,
		),
		array(
			'slug'     => 'cupping',
			'name'     => 'Cupping Therapy',
			'group'    => 'body',
			'image'    => 'band-cupping.jpg',
			'alt'      => 'A cupping therapy session in a calm treatment room',
			'text'     => 'Dry cupping places suction cups along the back and shoulders to lift the tissue rather than press into it. The round marks it leaves are normal and usually fade within a few days.',
			'benefits' => 'Targets stubborn upper-back tightness
A deep, different sensation to massage
Pairs naturally with a back massage
A favourite for post-training recovery',
			'featured' => false,
		),
		array(
			'slug'     => 'gel-nails',
			'name'     => 'Gel Nails',
			'group'    => 'nails',
			'image'    => 'svc-gel-nails.jpg',
			'alt'      => 'Deep burgundy gel nails, freshly finished',
			'text'     => 'Careful prep, precise shaping and a smooth, glass-like gel finish in the colour you have been saving a photo of. Sealed properly so it wears without lifting or chipping.',
			'benefits' => 'High-shine finish that lasts two to three weeks
Shaped to suit your own nail bed
Adds strength to natural nails
Touch-dry the moment you leave',
			'featured' => true,
		),
		array(
			'slug'     => 'extensions',
			'name'     => 'Nail Extensions',
			'group'    => 'nails',
			'image'    => 'svc-extensions.jpg',
			'alt'      => 'Long black nail extensions worn with gold rings',
			'text'     => 'Sculpted acrylic or builder-gel extensions in your preferred length and shape — almond, square, coffin or stiletto — balanced to the width of your natural nail.',
			'benefits' => 'Any length and shape you like
Strong enough for everyday wear
Infills available to keep the set going
Safe removal, never prised off',
			'featured' => false,
		),
		array(
			'slug'     => 'mani-pedi',
			'name'     => 'Manicure & Pedicure',
			'group'    => 'nails',
			'image'    => 'svc-mani-pedi.jpg',
			'alt'      => 'A freshly finished pedicure',
			'text'     => 'Classic shaping, cuticle work, buffing and a flawless polish. The pedicure adds a soak, hard-skin care and a warm massage.',
			'benefits' => 'Tidy, healthy nails and cuticles
Hard-skin and callus care
Hydrating hand and foot massage
Regular or long-wear gel finish',
			'featured' => false,
		),
		array(
			'slug'     => 'makeup',
			'name'     => 'Makeup & Glam',
			'group'    => 'glam',
			'image'    => 'svc-makeup.jpg',
			'alt'      => 'A clean, luminous glam makeup finish',
			'text'     => 'Skin prepped and primed first, then a base matched in natural light so it never turns grey in photographs. Day looks, occasions, bridal and bridal parties.',
			'benefits' => 'Shade matched in daylight
Long-wear formulas for a full day
Lashes chosen to suit your eye shape
Group and bridal-party timings available',
			'featured' => true,
		),
		array(
			'slug'     => 'brows',
			'name'     => 'Brow & Lash Finish',
			'group'    => 'glam',
			'image'    => 'svc-brows.jpg',
			'alt'      => 'Defined brows and lashes, close up',
			'text'     => 'Brows mapped to your features, shaped, tidied and tinted if you want more depth — with lashes chosen to match.',
			'benefits' => 'Mapped to your face, not a template
Defines the eyes without heavy makeup
Tint for extra depth where you want it
Pairs well with a makeup or facial booking',
			'featured' => false,
		),
	);
}
