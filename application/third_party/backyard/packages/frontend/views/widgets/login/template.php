<div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg"></p>

              
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="account" placeholder="帳號">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name="password" placeholder="密碼">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="login btn btn-primary btn-block">登 入</button>
                        </div>
                    </div>
            </div>
            <!-- /.login-card-body -->
        </div>
<script>
    $('document').ready(function() {
        $('div[widget="{code}"]').widget_login({
            'userType': '{userType}'
        });
    });
</script>
<!-- /.card -->