<!-- BEGIN MODAL POPUP -->
<div id="targetsalesman" class="modal container fade" tabindex="false" data-width="760" role="dialog">
    <div class="modal-header" style="margin-top: 30px;margin-left: 30px;margin-right: 30px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><b><span id="title">ADD NEW</span> TARGET SALESMAN</b></h4>
    </div>
    <div class="modal-body" style="margin-bottom: 30px;margin-left: 30px;margin-right: 30px;">

        <form id="form_targetsalesman" class="form-horizontal" action="{{ url('targetsalesman') }}" method="POST">
                    {{ csrf_field() }}
                    @if (!empty($data))
                      {{ method_field('PATCH') }}
                    @endif
                    <div class="form-body">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                        <br><br>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Promoter</label>
                          <div class="col-sm-8">

                          <div class="input-group" style="width: 100%;">

                                <select class="select2select" name="user_id" id="promoter" required></select>

                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

                            </div>

                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Target Call</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="target_call" name="target_call" class="form-control" placeholder="Input Target Call" data-tooltip="true" value="0" />
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Target Active Outlet</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="target_active_outlet" name="target_active_outlet" class="form-control" placeholder="Input Target Active Outlet" data-tooltip="true" value="0" />
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Target Effective Call</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="target_effective_call" name="target_effective_call" class="form-control" placeholder="Input Target Effective Call" data-tooltip="true" value="0" />
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Target Sales</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="target_sales" name="target_sales" class="form-control" placeholder="Input Target Sales" data-tooltip="true" value="0" />
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Target Sales PF</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="target_sales_pf" name="target_sales_pf" class="form-control" placeholder="Input Target Sales PF" data-tooltip="true" value="0" />
                            </div>
                          </div>
                        </div>

                        <div class="form-group" style="padding-top: 15pt;">
                          <div class="col-sm-8 col-sm-offset-3">
                            <button type="submit" class="btn btn-primary green">Save</button>
                          </div>
                        </div>

                    </div>
                </form>

    </div>
    <div class="modal-footer" style="margin-bottom: 30px;margin-left: 30px;margin-right: 30px;">
        <button type="button" data-dismiss="modal" class="btn btn-outline dark">Close</button>
    </div>
</div>
<!-- END MODAL POPUP -->