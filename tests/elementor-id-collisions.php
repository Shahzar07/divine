<?php
/** Guard: in Elementor a section id and a control id share one namespace. */
require_once __DIR__ . '/wp-load.php';
do_action('elementor/loaded');
$all = \Elementor\Plugin::$instance->widgets_manager->get_widget_types();
$bad = 0;
foreach ($all as $name => $w) {
    if (!str_starts_with($name, 'divine-')) continue;
    $sections = [];
    $controls = [];
    foreach ($w->get_controls() as $id => $c) {
        if (($c['type'] ?? '') === 'section') $sections[] = $id; else $controls[] = $id;
    }
    foreach (array_intersect($sections, $controls) as $clash) {
        echo "COLLISION  $name: '$clash' is both a section and a control\n";
        $bad++;
    }
}
echo $bad ? "$bad collision(s)\n" : "no section/control id collisions across " . count(array_filter(array_keys($all), fn($k) => str_starts_with($k,'divine-'))) . " widgets\n";
exit($bad ? 1 : 0);
