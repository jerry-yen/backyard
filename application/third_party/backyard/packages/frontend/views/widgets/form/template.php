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
            <button type="button" class="submit btn btn-primary">儲存</button>
        </div>
    </form>
</div>
<script>
    $('document').ready(function() {
        $('div[widget="{code}"]').widget_form({
            'userType': '{userType}'
        });
    });
</script>
<!-- /.card -->