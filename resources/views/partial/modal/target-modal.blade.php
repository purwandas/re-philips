<!-- BEGIN MODAL POPUP -->
<div id="target" class="modal container fade" tabindex="false" data-width="760" role="dialog">
    <div class="modal-header" style="margin-top: 30px;margin-left: 30px;margin-right: 30px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><b><span id="title">ADD NEW</span> TARGET</b></h4>
    </div>
    <div class="modal-body" style="margin-bottom: 30px;margin-left: 30px;margin-right: 30px;">

        <form id="form_target" class="form-horizontal" action="{{ url('target') }}" method="POST">
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

                        <div id="partnerContent" class="display-hide">
                            <div class="form-group">
                                <label class="control-label col-md-3">Have partner?
                                </label>
                                <div class="col-md-8" style="padding-top: 10px;">
                                    <div class="mt-checkbox-list">
                                        <label class="mt-checkbox mt-checkbox-outline">
                                            <input id="partnerCheck" type="checkbox" name="partnerCheck"> Yes (Demonstrator Only)
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Store</label>
                          <div class="col-sm-8">

                          <div class="input-group" style="width: 100%;">

                                <select class="select2select" name="store_id" id="store" required></select>

                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

                            </div>

                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Sell Type</label>
                          <div class="col-sm-8">

                          <div class="input-group" style="width: 100%;">

                                <select class="select2select" name="sell_type" id="sell_type" required>
                                    <option value="Sell In" {{ (@$data->sell_type == 'Sell In') ? "selected" : "" }}>Sell Thru</option>
                                    <option value="Sell Out" {{ (@$data->sell_type == 'Sell Out') ? "selected" : "" }}>Sell Out</option>
                                </select>

                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

                            </div>

                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Target DA</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="target_da" name="target_da" class="form-control" placeholder="Input Target DA" data-tooltip="true" value="0" />
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Target PF DA</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="target_pf_da" name="target_pf_da" class="form-control" placeholder="Input Target PF DA" data-tooltip="true" value="0" />
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Target PC</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="target_pc" name="target_pc" class="form-control" placeholder="Input Target PC" data-tooltip="true" value="0" />
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Target PF PC</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="target_pf_pc" name="target_pf_pc" class="form-control" placeholder="Input Target PF PC" data-tooltip="true" value="0" />
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Target MCC</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="target_mcc" name="target_mcc" class="form-control" placeholder="Input Target MCC" data-tooltip="true" value="0" />
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Target PF MCC</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="target_pf_mcc" name="target_pf_mcc" class="form-control" placeholder="Input Target PF MCC" data-tooltip="true" value="0" />
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