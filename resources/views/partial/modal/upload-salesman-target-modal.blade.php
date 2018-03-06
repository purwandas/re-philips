<!-- BEGIN MODAL POPUP -->
<div id="upload-target" class="modal container fade" tabindex="false" data-width="760" role="dialog" data-backdrop="static">

    <div id="loading_element">

    <div class="modal-header" style="margin-top: 30px;margin-left: 30px;margin-right: 30px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><b>UPLOAD EXCEL FOR TARGET</b> </h4>
    </div>
    

    <div class="modal-body" style="margin-bottom: 30px;margin-left: 30px;margin-right: 30px;">

        <form id="form_upload" class="form-horizontal" action="{{ url('import-salesman-target') }}" method="POST">                
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
                          <label class="col-sm-3 control-label">Current Target</label>
                          <div class="col-sm-8">
                            <a id="exportTemplate" class="btn green-dark" >
                            <i class="fa fa-cloud-download"></i> DOWNLOAD DATA </a>
                          </div>
                        </div> 

                        <div class="form-group">
                          <label class="col-sm-3 control-label">File</label>
                          <div class="col-sm-8">
                            <div class="input-group" style="width: 100%;">
                              <input type="file" class="form-control" id="upload_file" name="upload_file" required="required">
                              <p style="font-size: 10pt;" class="help-block"> (Type of file: xls, xlsx, xlsb) </p>
                              <div class="file_error_message" style="display: none;"></div>
                          </div>
                          </div>
                        </div>                                                 

                        <div class="form-group" style="padding-top: 15pt;">
                          <div class="col-sm-8 col-sm-offset-3">
                            <button type="submit" class="btn btn-primary"><i
                        class="fa fa-cloud-upload"></i>&nbsp;&nbsp;UPLOAD DATA</button>                        
                          </div>
                        </div>

                          

                    </div>
                </form>

    </div>
    <div class="modal-footer" style="margin-bottom: 30px;margin-left: 30px;margin-right: 30px;">
        <button type="button" data-dismiss="modal" class="btn btn-outline dark">Close</button>
    </div>

    </div>
</div>
<!-- END MODAL POPUP -->