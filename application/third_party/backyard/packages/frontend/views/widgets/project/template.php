<div class="project-list mb-3 col-12 col-sm-12 col-md-4 col-lg-4 col-xl-3 float-left">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">專案清單</h4>
        </div>
        <div class="card-body">
            <!-- the events -->
            <ul class="project-list nav nav-pills flex-column">
                <li class="nav-item d-none">
                    <a href="#" class="nav-link">
                        <i class="fas fa-inbox"></i> <span class="title">榮欣社福</span><br />
                        <span class="badge bg-red float-right ml-1 delete-project"> <i class="far fa-trash-alt"></i></span>
                        <span class="badge bg-blue float-right ml-1 modify-project"> <i class="far fa-edit"></i></span>
                        <span class="badge bg-gray float-right progress">12 / 100</span>
                    </a>
                </li>
                <li class="nav-item sprint" id="sprint">
                    <a href="#" class="nav-link">
                        <i class="fas fa-running"></i> <span class="title">衝刺計畫</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- /.card-body -->
    </div>
    <button type="button" class="add-project btn bg-green" title="新增專案"><i class="fas fa-plus"></i></butotn>
</div>

<div class="sticky-top mb-3 col-12 col-sm-12 col-md-8 col-lg-8 col-xl-9 float-left">

    <div class="card card-primary table">
        <div class="card-header ">
            <h3 class="card-title">專案：<span class="project-title"></span> - 項目編輯</h3>
        </div>

        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
            <div class="card-tools" style="padding:5px;">
                模式：
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-secondary bg-white active" title="待辦清單">
                        <input type="radio" name="options" id="option_a1" autocomplete="off" checked> <i class="fas fa-list"></i>
                    </label>
                    <label class="btn btn-secondary bg-white" title="看板">
                        <input type="radio" name="options" id="option_a2" autocomplete="off"> <i class="fas fa-columns"></i>
                    </label>
                    <label class="btn btn-secondary bg-white" title="甘特圖">
                        <input type="radio" name="options" id="option_a3" autocomplete="off"> <i class="fas fa-stream"></i>
                    </label>
                </div>
            </div>
            <div class="todolist-view">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>操作</th>
                            <th>項目名稱</th>
                            <th>處理進度</th>
                            <th>截止日期</th>
                            <th>衝刺</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="nav-item d-none">
                            <td>
                                <button type="button" class="modify-item btn btn-xs bg-blue"><i class="far fa-edit"></i> 修改</button>
                                <button type="button" class="delete-item btn btn-xs bg-red"><i class="fas fa-trash-alt"></i> 刪除</button>
                            </td>
                            <td class="title">
                                項目1
                            </td>
                            <td class="task-progress">
                                12 / 100 (12%)
                            </td>
                            <td class="deadline">
                                2021-05-01
                            </td>
                            <td>
                                <button type="button" class="sprint btn btn-xs bg-gray"><i class="fas fa-running"></i> 衝刺</button>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
                <button type="button" class="add-item btn bg-green" title="新增項目"><i class="fas fa-plus"></i></butotn>
                    <div class="clearfix"></div>
            </div>
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
</div>

<div class="modal fade" id="project-info">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title">專案資訊</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="card-body">
                        
                    </div>
                    <!-- /.card-body -->

                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
                <button type="button" class="btn btn-primary save-project">儲存</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="item-info">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title">項目資訊</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title">項目名稱</label>
                            <input type="text" class="form-control" placeholder="請輸入專案名稱">
                        </div>
                        <div class="form-group">
                            <label for="startdate">開炲日期</label>
                            <input type="date" class="form-control" placeholder="請輸入開炲日期">
                        </div>
                        <div class="form-group">
                            <label for="deadline">截止日期</label>
                            <input type="date" class="form-control" placeholder="請輸入截止日期">
                        </div>
                        <div class="form-group">
                            <label for="why">需求原因</label>
                            <textarea class="form-control" id="why" placeholder="請輸入需求原因"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="why">任務清單</label>
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>操作</th>
                                        <th>項目名稱</th>
                                        <th>截止日期</th>
                                        <th>狀態</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="d-none">
                                        <td>
                                            <button type="button" class="modify btn btn-xs bg-blue d-none"><i class="far fa-edit"></i> 修改</button>
                                            <button type="button" class="delete btn btn-xs bg-red d-none"><i class="fas fa-trash-alt"></i> 刪除</button>
                                        </td>
                                        <td>
                                            項目1
                                        </td>
                                        <td>
                                            12 / 100
                                        </td>
                                        <td>
                                            2021-05-01
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td>
                                            <button type="button" class="btn btn-xs bg-blue modify-task"><i class="far fa-edit"></i> 修改</button>
                                            <button type="button" class="btn btn-xs bg-red delete-task"><i class="fas fa-trash-alt"></i> 刪除</button>
                                        </td>
                                        <td>
                                            項目1
                                        </td>
                                        <td>
                                            2021-03-20
                                        </td>
                                        <td>
                                            未處理
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->

                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
                <button type="button" class="btn btn-primary save-item">儲存</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<script>
    $('document').ready(function() {
        $('div[widget="{code}"]').backyard_project({
            'userType': '{userType}'
        });
    });
</script>