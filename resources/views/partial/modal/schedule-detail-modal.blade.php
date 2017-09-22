<!-- BEGIN MATCH ENTRY MODAL POPUP -->
<div id="schedule-detail" class="modal container fade" tabindex="false" data-width="850" role="dialog">
    <div class="modal-header" style="margin-top: 30px;margin-left: 30px;margin-right: 30px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><b> ADD SCHEDULE DETAIL</b></h4>
    </div>
    <div class="modal-body" style="margin-bottom: 30px;margin-left: 30px;margin-right: 30px;">
        
        <form id="form_schedule_details" class="form-horizontal" action="{{ url('scheduledetails') }}" method="POST">                
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
                          <label class="col-sm-2 control-label">Schedule</label>
                          <div class="col-sm-9">

                          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="schedule_id" id="detailsSchedule" required></select>
                                
                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

                            </div>
                            
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-2 control-label">Match Entry</label>
                          <div class="col-sm-9">

                          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="matchentry_id[]" id="detailsMatchEntry" multiple="multiple" required></select>
                                
                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

                            </div>
                            
                          </div>
                        </div>                                                                               

                        <div class="form-group" style="padding-top: 15pt;">
                          <div class="col-sm-9 col-sm-offset-2">
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
<!-- END MATCH ENTRY MODAL POPUP -->