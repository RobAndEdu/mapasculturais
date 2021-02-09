<?php

use MapasCulturais\i;

/**
 * 
    $this->part('charts/line', [
        'vertical',
        'labels' => ['Draft', 'Pending', 'Publish'],
        'series' => [
           ['label' => 'Preto', 'data' => [66,120,70], 'color' => 'black'],
           ['label' => 'Branco', 'data' => [22,44,66], 'color' => 'white'],
           ['label' => 'Amarelo', 'data' => [77,55,34], 'color' => 'yellow']
        ]
    ]);
 * 
 */
$title = $title ?? null;
$chart_id = uniqid('chart-line-');
$datasets = [];

if (isset($series) && is_array($series)) {
    $datasets = array_map(function($dataset) {
        
        $dataset['fill'] = $dataset['fill'] ?? false;
        $dataset['pointBorderWidth'] = $dataset['pointBorderWidth'] ?? '0';
        $dataset['radius'] = $dataset['radius'] ?? '4';
        $dataset['hoverRadius'] = $dataset['hoverRadius'] ?? $dataset['radius'] + 1;

        if(isset($dataset['colors'])) {
            $dataset['borderColor'] = $dataset['borderColor'] ?? $dataset['colors'];
            $dataset['pointBackgroundColor'] = $dataset['pointBackgroundColor'] ?? $dataset['colors'];
            unset($dataset['colors']);
        }
        return $dataset;

    }, $series);
}

$width = $width ?? '50vw';
$height = $height ?? '50vw';
$route = MapasCulturais\App::i()->createUrl('reports', $action, ['opportunity_id' => $opportunity->id]); 
?>
<div class="chart-container chart-line" style="position: relative; height:<?= $height ?>; width:<?= $width ?>;">
    <header>
        <?php if ($title) : ?>
            <h3><?= $title ?></h3>
        <?php endif; ?>
        <a href="<?=$route?>" class="btn btn-default download"><?php i::_e("Baixar em CSV"); ?></a>
    </header>
    
    <canvas id="<?= $chart_id ?>"></canvas>
    <?php $this->part('chart-legends', ["legends" => $legends, "colors" => $colors,'opportunity' => $opportunity]); ?>
</div>

<script>
    $(window).on('load', function() {
        var config = {
            type: 'line',
            data: {
                datasets: <?= json_encode($datasets) ?>,
                labels: <?= json_encode($labels) ?>,
            },
            options: {
                responsive: true,
                legend: false,
                plugins: {
                    datalabels: {
                        display: false,
                        
                    }
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display:false
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            borderDash: [5, 5],
                        }
                    }]
                }
            }
        };
        
        config.data.datasets.forEach(function(dataset) {
            dataset.backgroundColor = dataset.backgroundColor || MapasCulturais.Charts.dynamicColors();
        });

        var ctx = document.getElementById("<?= $chart_id ?>").getContext('2d');
        ctx.canvas.width = 1000;
		ctx.canvas.height = 300;
        MapasCulturais.Charts.charts["<?= $chart_id ?>"] = new Chart(ctx, config);
    });
</script>