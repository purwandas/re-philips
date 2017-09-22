<!-- BEGIN MATCH ENTRY MODAL POPUP -->
<div id="match-entry" class="modal container fade" tabindex="false" data-width="760" role="dialog">
    <div class="modal-header" style="margin-top: 30px;margin-left: 30px;margin-right: 30px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><b><span id="matchEntryTitle">ADD</span> MATCH ENTRY</b></h4>
    </div>
    <div class="modal-body" style="margin-bottom: 30px;margin-left: 30px;margin-right: 30px;">
        
        <form id="form_match_entries" class="form-horizontal" action="{{ url('matchentries') }}" method="POST">                
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
                          <label class="col-sm-3 control-label">Match Entry Code</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input id="matchEntryCode" type="text" name="code" class="form-control" value="" readonly />
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Type Sport</label>
                          <div class="col-sm-8">

                          <div class="input-group" style="width: 100%;">
     
                                <select class="select2select" name="typesport_id" id="matchEntryTypeSport" required></select>
                                
                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

                            </div>
                            
                          </div>
                        </div>                                                      
                        
                        <div class="form-group">
                          <label class="col-sm-3 control-label">Description</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <textarea id="matchEntryDescription" name="description" class="form-control" rows="5" placeholder="Description about this match entry (required)"></textarea>
                            </div>
                          </div>
                        </div>

                        <div class="form-group" style="padding-top: 15pt;">
                          <div class="col-sm-9 col-sm-offset-3">
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