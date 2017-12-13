<!-- BEGIN MODAL POPUP -->
<div id="feedbackQuestion" class="modal container fade" tabindex="false" data-width="760" role="dialog">
    <div class="modal-header" style="margin-top: 30px;margin-left: 30px;margin-right: 30px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><b><span id="title">ADD NEW</span> FeedbackQuestion</b></h4>
    </div>
    <div class="modal-body" style="margin-bottom: 30px;margin-left: 30px;margin-right: 30px;">
        
        <form id="form_feedbackQuestion" class="form-horizontal" action="{{ url('feedbackQuestion') }}" method="POST">                
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
                          <label class="col-sm-3 control-label">Feedback Category</label>
                          <div class="col-sm-8">

                          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="feedbackCategory_id" id="feedbackCategory" required></select>
                                
                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

                            </div>
                            
                          </div>
                        </div> 

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Question</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="question" name="question" class="form-control" placeholder="Input FeedbackCategory Name" data-tooltip="true" />
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Type</label>
                          <div class="col-sm-8">
                            <div class="input-icon" style="width: 100%;">
                                <select class="select2select" name="type" id="type" required>
                                    <option value="PK" {{ (@$data->type == 'PK') ? "selected" : "" }}>PK</option>
                                    <option value="POG" {{ (@$data->type == 'POG') ? "selected" : "" }}>POG</option>
                                    <option value="POSM" {{ (@$data->type == 'POSM') ? "selected" : "" }}>POSM</option>                                                             
                                </select>
                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>
                                
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