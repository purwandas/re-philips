<!-- BEGIN MODAL POPUP -->
<div id="quizTarget" class="modal container fade" tabindex="false" data-width="760" role="dialog">
    <div class="modal-header" style="margin-top: 30px;margin-left: 30px;margin-right: 30px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><b><span id="title">ADD NEW</span> Target</b></h4>
    </div>
    <div class="modal-body" style="margin-bottom: 30px;margin-left: 30px;margin-right: 30px;">
        
        <form id="form_quiz_target" class="form-horizontal" action="{{ url('quiz-target') }}" method="POST">                
                    {{ csrf_field() }}
                    <div class="form-body">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button> Your form validation is successful! </div>                    
                        <br><br>
                        
                        <div class="form-group">
                          <label class="col-sm-3 control-label">User Role</label>
                          <div class="col-sm-8">

                          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="role_id" id="role" required></select>
                                
                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

                            </div>
                            
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">User Grading</label>
                          <div class="col-sm-8">

                          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="grading_id" id="grading" required></select>
                                
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