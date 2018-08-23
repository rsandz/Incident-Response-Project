<section class="section">
    <h1 class="title"><?php echo $project->project_name?></h2>
    <hr>
    <div class="columns">
        <div class="column is-9">
            <div class="box is-fullheight">
                <h2 class="subtitle">Project Info:</h2>
                <div class="content">
                    <ul>
                        <li>
                            Project Leader: 
                            <a href="mailto:<?php echo $project_leader->email?>">
                                <?php echo $project_leader->name?>
                            </a>
                        </li>
                        <li>
                            Project Description: <?php echo $project->project_desc?>
                        </li>
                        <li>Active Project?: <?php echo humanize($project->is_active)?></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="column is-3">
            <div class="box is-fullheight">
                <h2 class="subtitle">Controls:</h2>
                <div class="field">
                    <div class="control">
                        <a href="<?php echo site_url('Stats/project_stats/'.$project->project_id)?>" 
                            class="button is-info is-fullwidth">
                            <span class="icon"><i class="fas fa-chart-bar fa-sm"></i></span>
                            <span>View Statistics</span>
                        </a>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <?php if ($this->authentication->check_admin()):?>
                        <a href="<?php echo site_url('Modify/projects/'.$project->project_id)?>"
                            class="button is-info is-fullwidth">
                            <span class="icon"><i class="fas fa-cogs fa-sm"></i></span>
                            <span>Modify Project</span>
                        </a>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box">
        <h2 class="subtitle">Log Activity</h2>
        <hr>
        <h3 class="subtitle is 3">Active Teams:</h3>
        <div class="box">
            <?php echo $active_teams_table?>
        </div>
    </div>
</section>
    