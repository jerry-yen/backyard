<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <!--
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="{adminlte}/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
            <a href="#" class="d-block">Alexander Pierce</a>
        </div>
    </div>
    -->
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- <li class="nav-header">MISCELLANEOUS</li> -->
            <li class="nav-item d-none template">
                <a href="#" class="nav-link">
                    <!-- active -->
                    <i class="fas fa-th-list" style="width:30px;text-align:center;"></i>
                    <p>Dashboard v1</p>
                </a>
            </li>
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->

<script>
    $('document').ready(function() {
        $('div[widget="{code}"]').widget_menu({
            'userType': '{userType}',
            'uri' : '{uri}'
        });
    });
</script>