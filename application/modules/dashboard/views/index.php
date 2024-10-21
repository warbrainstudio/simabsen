<style type="text/css">
    .img-home {
        margin-top: 1rem;
        width: <?= isset($app->dashboard_image_width) ? $app->dashboard_image_width . '%' : '100%' ?>;
        max-height: <?= isset($app->dashboard_image_max_height) ? $app->dashboard_image_max_height . 'px' : '450px' ?>;
        object-fit: <?= isset($app->dashboard_image_object_fit) ? $app->dashboard_image_object_fit : 'cover' ?>;
        object-position: <?= isset($app->dashboard_image_object_position) ? $app->dashboard_image_object_position : 'center' ?>;
        box-shadow: <?= (isset($app->dashboard_image_box_shadow) && $app->dashboard_image_box_shadow === '1') ? '0 1px 2px rgba(0, 0, 0, 0.1)' : 'none' ?>;
    }

    .text-small {
        font-size: 1rem;
        display: block;
        color: rgba(255, 255, 255, .8);
        font-weight: 600;
    }

    .flot-chart--xs {
        color: rgba(255, 255, 255, 0.6);
        font-size: 1.2rem;
        text-align: center;
        text-shadow: 0px 1px rgba(1, 1, 1, 0.1);
        font-weight: 500;
    }

    .stats__info h2 {
        font-size: 1.1rem;
        font-weight: 300;
    }

    @media only screen and (max-width: 768px) {
        .img-home {
            margin-top: 1rem;
            width: 100%;
            max-height: <?= isset($app->dashboard_image_max_height) ? $app->dashboard_image_max_height . 'px' : '450px' ?>;
            object-fit: <?= isset($app->dashboard_image_object_fit) ? $app->dashboard_image_object_fit : 'cover' ?>;
            object-position: <?= isset($app->dashboard_image_object_position) ? $app->dashboard_image_object_position : 'center' ?>;
            box-shadow: <?= (isset($app->dashboard_image_box_shadow) && $app->dashboard_image_box_shadow === '1') ? '0 1px 2px rgba(0, 0, 0, 0.1)' : 'none' ?>;
        }
    }

    .calendar {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    .calendar .header_month {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        padding: 10px 0;
    }

    .calendar .header_day {
        text-align: center;
        vertical-align: middle;
        font-size: 24px;
        padding: 10px 0;
    }

    .calendar .day,
    .calendar .today {
        font-size: 24px;
        height: 80px; 
        text-align: center;
        vertical-align: middle;
        border: 1px solid #ddd;
        position: relative;
        transition: background-color 0.3s;
    }

    .calendar .day:hover {
        background-color: #f0f0f0;
    }

    .calendar .day .no_content_fill_day {
        color: black;
    }

    .calendar .today .no_content_fill_today {
        color: white;
    }

    .calendar .today {
        background-color: #13c8e8; 
        font-weight: bold;
    }

    .calendar .day .date {
        display: block;
        margin: 10px 0;
        font-size: 20px;
    }

    .calendar .day .events {
        font-size: 14px;
        color: #666;
    }
</style>
<section id="dashboard">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Dashboard</h4>
            <h6 class="card-subtitle">Selamat datang di <?= $app->app_name ?> v<?= $app->app_version ?></h6>
        </div>
        <div class="card-body">
                <div class="row">
                    <div class="col">
                        <?php include_once('form.php') ?>
                        <center>
                            <?php echo $calendar ?>
                        </center>
                    </div>
                </div>
        </div>
    </div>
</section>