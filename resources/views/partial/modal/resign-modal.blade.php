<!-- BEGIN MODAL POPUP -->
<div id="resign" class="modal container fade" tabindex="false" data-width="760" role="dialog">
    <div class="modal-header" style="margin-top: 30px;margin-left: 30px;margin-right: 30px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><b>CONFIRM RESIGN</b></h4>
    </div>
    <div class="modal-body" style="margin-bottom: 30px;margin-left: 30px;margin-right: 30px;">

        <form id="form_resign" class="form-horizontal" action="{{ url('resign') }}" method="POST">
                    {{ csrf_field() }}
                    @if (!empty($data))
                      {{ method_field('PATCH') }}
                    @endif
                    <div class="form-body">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                        <br>

                        <div class="form-group">
                          <label class="col-sm-3" style="text-align: right;"><b>NIK</b></label>
                          <div class="col-sm-8"><span id="nik">NIK</span></div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3" style="text-align: right;"><b>NAME</b></label>
                          <div class="col-sm-8"><span id="name">NAME</span></div>
                        </div>              

                        <div class="form-group">
                          <label class="col-sm-3" style="text-align: right;"><b>GRADING</b></label>
                          <div class="col-sm-8"><span id="grading">GRADING</span></div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3" style="text-align: right;"><b>JOIN DATE</b></label>
                          <div class="col-sm-8"><span id="join_date">JOIN DATE</span></div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3" style="text-align: right;"><b>ROLE</b></label>
                          <div class="col-sm-8"><span id="role">ROLE</span></div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3" style="text-align: right;"><b>STATUS</b></label>
                          <div class="col-sm-8"><span id="status">STATUS</span></div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label"><b>ALASAN RESIGN</b></label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="name" name="alasan_resign" class="form-control" placeholder="Alasan Resign" />
                            </div>
                          </div>
                        </div>

                        <input type="hidden" name="employeeId" id="employeeId" value="">

                        <div class="form-group" style="padding-top: 15pt;">
                          <div class="col-sm-9 col-sm-offset-3">
                            <button type="submit" class="btn btn-primary green">Confirm Resign</button>
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