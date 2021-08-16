<!-- general form elements -->
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"></h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form">
        <div class="card-body">
            
        </div>
        <!-- /.card-body -->

        <div class="card-footer">
            <button type="button" class="modify btn btn-primary">修改</button>
        </div>
    </form>
</div>
<script>
    $('document').ready(function() {
        $('div[widget="{code}"]').backyard_form({
            'userType': 'master'
        });
    });
</script>
<!-- /.card -->