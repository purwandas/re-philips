<!-- BEGIN MODAL POPUP -->
<div id="roleModal" class="modal container fade" tabindex="false" data-width="760" role="dialog">
    <div class="modal-header" style="margin-top: 30px;margin-left: 30px;margin-right: 30px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><b><span id="title">ADD NEW</span> Role</b></h4>
    </div>
    <div class="modal-body" style="margin-bottom: 30px;margin-left: 30px;margin-right: 30px;">
        
        <form id="form_role" class="form-horizontal" action="{{ url('role') }}" method="POST">                
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
                          <label class="col-sm-3 control-label">Role Name</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="role" name="role" class="form-control" placeholder="Input Role" data-tooltip="true" />
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Function Type</label>
                          <div class="col-sm-8">

                          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="role_group" id="role_group" required>
                                  <option></option>
                                  <option value="Driver">Driver</option>
                                  <option value="Helper">Helper</option>
                                  <option value="PCE">PCE</option>
                                  <option value="RE Executive">RE Executive</option>
                                  <option value="RE Support">RE Support</option>
                                  <option value="Supervisor">Supervisor</option>
                                  <option value="Trainer">Trainer</option>
                                  <option value="Head Trainer">Head Trainer</option>
                                  <option value="Supervisor Hybrid">Supervisor Hybrid</option>
                                  <option value="DM">DM</option>
                                  <option value="RSM">RSM</option>
                                  <option value="Admin">Admin</option> 
                                  <option value="Promoter">Promoter</option>
                                  <option value="Promoter Additional">Promoter Additional</option>
                                  <option value="Promoter Event">Promoter Event</option>
                                  <option value="Demonstrator MCC">Demonstrator MCC</option>
                                  <option value="Demonstrator DA">Demonstrator DA</option>
                                  <option value="ACT">ACT</option>
                                  <option value="PPE">PPE</option>
                                  <option value="BDT">BDT</option>
                                  <option value="Salesman Explorer">Salesman Explorer</option>
                                  <option value="SMD">SMD</option>
                                  <option value="SMD Coordinator">SMD Coordinator</option>
                                  <option value="HIC">HIC</option>
                                  <option value="HIE">HIE</option>
                                  <option value="SMD Additional">SMD Additional</option>
                                  <option value="ASC">ASC</option>
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
<!-- END MODAL POPUP