<div class="columns page-content">
  <!--BULMA Sidebar from https://codepen.io/andreich1980/pen/OmobJQ-->
  <aside class="sidebar column is-2 is-narrow section is-hidden-mobile has-background-light">
    <p class="menu-label is-hidden-touch">Navigation</p>
    <ul class="menu-list">
      <li>
        <a href="<?php echo site_url('Dashboard')?>" 
          class="<?php if ($title == 'Dashboard') echo 'is-active'?>">
          <span class="icon"><i class="fa fa-home"></i></span> Dashboard
        </a>
      </li>
      <li>
        <a href="<?php echo site_url('Logging')?>" 
          class="<?php if ($title == 'Logging Form') echo 'is-active'?>">
          <span class="icon"><i class="fas fa-pencil-alt"></i></span> Log an Activity
        </a>
      </li>
      <li class='has-sub-menu'>
        <a>
            <span class="icon"><i class="fas fa-chart-bar"></i></span> Statistics
        </a>

        <ul class="sub-menu">
          <li>
            <?php $attr = ($title == 'My Statistics') ? 'is-active' : NULL?>
		      	<?php echo anchor('Stats/my-stats', 'My Statistics', "class='{$attr}'");?>
          </li>
          <li>
            <?php $attr = ($title == 'Project Statistics') ? 'is-active' : NULL?>
            <?php echo anchor('Stats/project-stats', 'Project', "class='{$attr}'");?>
          </li>
          <li>
            <?php $attr = ($title == 'Team Statistics') ? 'is-active' : NULL?>
            <?php echo anchor('Stats/team-stats', 'Team', "class='{$attr}'");?>
          </li>
          <li>
            <?php $attr = ($title == 'Custom Statistic 1') ? 'is-active' : NULL?>
            <?php echo anchor('Stats/custom/1', 'Custom 1', "class='{$attr}'");?>
          </li>
          <li>
            <?php $attr = ($title == 'Custom Statistic 2') ? 'is-active' : NULL?>
            <?php echo anchor('Stats/custom/2', 'Custom 2', "class='{$attr}'");?>
          </li>
          <li>
            <?php $attr = ($title == 'Compare Statistics') ? 'is-active' : NULL?>
            <?php echo anchor('Stats/compare', 'Compare', "class='{$attr}'");?>
          </li>
        </ul>
      </li>
      <li class='has-sub-menu'>
        <a>
          <span class="icon"><i class="fas fa-cogs"></i></span> Create
        </a>
        
        <ul class='sub-menu'>
          <li>
            <?php $attr = ($title == 'Create Action') ? 'is-active' : NULL?>
            <?php echo anchor('Create/action', 'Action', "class='{$attr}'")?>
          </li>
          <!-- Only for Admins and team leaders -->
          <?php if (!$this->authentication->check_privileges('user')):?>
            <li>
              <?php $attr = ($title == 'Create Action Type') ? 'is-active' : NULL?>
              <?php echo anchor('Create/action_type', 'Action Type', "class='{$attr}'");?>
            </li>
            <li>
              <?php $attr = ($title == 'Create Project') ? 'is-active' : NULL?>
              <?php echo anchor('Create/project', 'Project', "class='{$attr}'");?>
            </li>
            <li>
              <?php $attr = ($title == 'Create Team') ? 'is-active' : NULL?>
              <?php echo anchor('Create/team', 'Team', "class='{$attr}'");?>
            </li>
            <li>
              <?php $attr = ($title == 'Create User') ? 'is-active' : NULL?>
              <?php echo anchor('Create/user', 'User', "class='{$attr}'");?>
            </li>
          <?php endif;?>
          <!-- End of Only for Admins and team leaders -->
        </ul>
      </li>
      <li class="has-sub-menu">
        <a>
          <span class="icon"><i class="fas fa-users"></i></span> Manage
        </a>

        <ul class='sub-menu'>
          <li>
            <?php $attr = ($title == 'Manage Teams') ? 'is-active' : NULL?>
            <?php echo anchor('Manage/teams', 'Manage Teams', "class='{$attr}'");?>
            <?php $attr = ($title == 'Manage Projects') ? 'is-active' : NULL?>
            <?php echo anchor('Manage/projects', 'Manage Projects', "class='{$attr}'");?>
            <?php if ($this->authentication->check_admin()):?>
              <?php $attr = ($title == 'Manage Users') ? 'is-active' : NULL?>
              <?php echo anchor('Manage/users', 'Manage Users', "class='{$attr}'");?>
            <?php endif;?>
          </li>
        </ul>
      </li>
      <?php if($this->authentication->check_admin()):?>
        <li class="has-sub-menu">
          <a>
            <span class="icon"><i class="fas fa-key"></i></span> 
            Admin
          </a>

            <ul class='sub-menu'>
              <li>
                <?php $attr = ($title == 'Modify') ? 'is-active' : NULL?>
                <?php echo anchor('Modify', 'Modify', "class='{$attr}'");?>
              </li>
              <li>
                <?php $attr = ($title == 'Incidents') ? 'is-active' : NULL?>
                <?php echo anchor('Incidents', 'Incidents', "class='{$attr}'");?>
              </li>
              <li>
                <?php $attr = ($title == 'Site Settings') ? 'is-active' : NULL?>
                <?php echo anchor('Admin/site-settings', 'Site Settings', "class='{$attr}'")?>
              </li>
            </ul>
        </li>
      <?php endif;?>
    </ul>
  </aside>
  <!-- End of Sidebar-->

  <div class="column is-10 main-content">
    <!-- Place holder for displaying errors -->
    <?php if (isset($errors)) echo $errors?>
    <!-- Placeholder for notifications -->
    <?php if (isset($notifications)) echo $notifications?>
    <!-- Page content -->
    <?php echo $content?>
  </div>
</div>

<script>
  window.lastSidebarItem = '';
  $(function() {
    /* Toggles 'is-active' for sub menus. (i.e. stats create etc.)*/
    $('.has-sub-menu').click(function() {
      if (this != window.lastSidebarItem) $(window.lastSidebarItem).removeClass('is-active');
      $(this).toggleClass('is-active');
      window.lastSidebarItem = this;
    });

    /* Toggles 'is-active' for current sub menus*/
    $('.has-sub-menu').find('.is-active').each(function() {
      $(this).parentsUntil('.menu-list').toggleClass('is-active');
      window.lastSidebarItem = $(this).parentsUntil('.menu-list');
    });
  });
</script>