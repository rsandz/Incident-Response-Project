<section class="section">
    <h2 class="title"><?php echo $user->name?></h2>
    <hr>
    <div class="columns">
        <div class="column is-9">
            <div class="box is-fullheight">
                <h2 class="subtitle">User Info</h2>
                <div class="content">
                    <ul>
                        <li>
                            Email:
                            <a href="mailto:<?php echo $user->email?>"><?php echo $user->email?></a>
                        </li>
                        <li>
                            Phone Number:
                            <?php echo !empty($user->phone_num) ? $user->phone_num : 'Not set' ?>
                        </li>
                        <li>
                            Date Created:
                            <?php echo $user->date_created?>
                        </li>
                        <li>
                            About:
                            <?php echo $user->user_desc?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="column is-3">
            <div class="box is-fullheight">
            </div>
        </div>
    </div>
    <div class="box">
        <h2 class="subtitle">User Activity</h2>
        <hr>
        <div class="content">
            <ul>
                <li>Total Logs: <?php echo $stats['total_logs']?></li>
            </ul>
        </div>
        <h3 class="subtitle">Last Log:</h3>
        <div class="box">
            <?php echo $stats['last_log']?>
        </div>
        <h3 class="subtitle">Action Frequency</h3>
        <div class="box">
            <?php echo $stats['action_ranking']?>
        </div>
    </div>
</section>