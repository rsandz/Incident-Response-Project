<section class="section">
    <div class="level">
        <div class="level-left">
            <div class="level-item">
                <h1 class="title">Welcome to your Dashboard, <?php echo $name?></h1>
            </div>
        </div>
        <div class="level-right">
            <div class="level-item">
                Today is <?php echo date('l, d \of M Y')?>
            </div>
        </div>
    </div>
    <hr>
    <div class="columns">
        <div class="column is-9">
            <div class="box">
                <h2 class="subtitle">Notifications and Messages:</h2>
                <div class="content">
                    <ul>
                        <li>
                            <?php echo isset($global_notification) ? 
                                $global_notification : 'No Messages or Notifications';?>
                        </li>
                    </ul>
                </div>
                <hr>
                <h2 class="subtitle">Today's Overview:</h2>
                <div class="content">
                    <ul>
                        <li>You have made <strong><?php echo $user_logs_today?></strong> logs today.</li>
                        <li>You have logged <strong><?php echo $user_hours_today?></strong> hours today.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="column is-3">
            <div class="box">
                <h2 class="subtitle">Need Help?</h2>
                <div class="content">
                    <ul>
                        <li><a href="https://github.com/rsandz/step_project/wiki/HTML-Markup">Wiki</a></li>
                        <li>Email your 
                            <a href="mailto:<?php echo $this->config->item('admin_email')?>">Administrator</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>