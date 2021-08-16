<div class="card card-primary table">
    <div class="card-header ">
        <h3 class="card-title"></h3>
    </div>

    <!-- /.card-header -->
    <div class="card-body table-responsive p-0">
        <div class="card-tools" style="padding:5px;">
            <button type="button" class="add btn bg-green d-none"><i class="fas fa-plus"></i> 新增</button>
            <button type="button" class="batch_delete btn bg-red d-none"><i class="fas fa-trash-alt"></i> 刪除</button>
            <button type="button" class="sort btn bg-gradient-info d-none"><i class="fas fa-sort-amount-down-alt"></i> 排序</button>
            <button type="button" class="export btn bg-maroon d-none"><i class="fas fa-download"></i> 匯出</button>
            <button type="button" class="import btn bg-purple d-none"><i class="fas fa-upload"></i> 匯入</button>
            <button type="button" class="return btn bg-yellow d-none"><i class="fas fa-arrow-alt-circle-left"></i> 回上一層</button>
        </div>
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <tr class="d-none">
                    <td> 
                        <button type="button" class="modify btn btn-xs bg-blue d-none"><i class="far fa-edit"></i> 修改</button>
                        <button type="button" class="delete btn btn-xs bg-red d-none"><i class="fas fa-trash-alt"></i> 刪除</button>
                        <button type="button" class="list btn btn-xs bg-gradient-info d-none"><i class="fas fa-bars"></i> <span class="btitle">瀏覽</span></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">
        <ul class="pagination pagination-sm m-0 float-right">
            <li class="page-item prev d-none"><a class="page-link" href="javascript:void(0);">&laquo;</a></li>
            <li class="page-item number d-none"><a class="page-link" href="javascript:void(0);">1</a></li>
            <li class="page-item next d-none"><a class="page-link" href="javascript:void(0);">&raquo;</a></li>
        </ul>
    </div>
</div>

<!-- general form elements -->
<div class="card card-primary form d-none">
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
<!-- /.card -->


<div class="card card-primary sort d-none">
    <div class="card-header ">
        <h3 class="card-title"></h3>
    </div>

    <!-- /.card-header -->
    <div class="card-body table-responsive p-0">
        <div class="card-tools" style="padding:5px;">
            <button type="button" class="check-sort btn bg-blue"><i class="fas fa-check"></i> 修改順序</button>
            <button type="button" class="sort-return btn bg-yellow"><i class="fas fa-times"></i> 取消</button>
        </div>
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th width="20">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr class="d-none">
                    <td> 
                        <div class="sort-drop"><i class="fas fa-grip-vertical"></i></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">
        
    </div>
</div>

<script>
    $('document').ready(function() {
        $('div[widget="{code}"]').widget_data({
            'userType': '{userType}'
        });
    });
</script>