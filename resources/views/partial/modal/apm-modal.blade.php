<!-- BEGIN MODAL POPUP -->
<div id="apmmonth" class="modal container fade" tabindex="false" data-width="760" role="dialog">
    <div class="modal-header" style="margin-top: 30px;margin-left: 30px;margin-right: 30px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><b>SET MONTH FOR APM</b></h4>
    </div>
    <div class="modal-body" style="margin-bottom: 30px;margin-left: 30px;margin-right: 30px;">
        
        <form id="form_apm" class="form-horizontal" action="{{ url('apm') }}" method="POST">                
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
                            <label class="control-label col-md-4">SO Value {{ $arMonth[0] }}</label>
                            <div class="col-md-7">
                                <input name="month1" type="checkbox" class="make-switch" data-on-color="success" data-on-text="Yes&nbsp;" data-off-text="No" {{ ($apmMonth->first()->selected == 1) ? 'checked' : ''}}>
                                </div>
                        </div>  

                        <div class="form-group">
                            <label class="control-label col-md-4">SO Value {{ $arMonth[1] }}</label>
                            <div class="col-md-7">
                                <input name="month2" type="checkbox" class="make-switch" data-on-color="success" data-on-text="Yes&nbsp;" data-off-text="No" {{ ($apmMonth->get(1)->selected == 1) ? 'checked' : ''}}>
                                </div>
                        </div> 

                        <div class="form-group">
                            <label class="control-label col-md-4">SO Value {{ $arMonth[2] }}</label>
                            <div class="col-md-7">
                                <input name="month3" type="checkbox" class="make-switch" data-on-color="success" data-on-text="Yes&nbsp;" data-off-text="No" {{ ($apmMonth->get(2)->selected == 1) ? 'checked' : ''}}>
                                </div>
                        </div> 

                        <div class="form-group">
                            <label class="control-label col-md-4">SO Value {{ $arMonth[3] }}</label>
                            <div class="col-md-7">
                                <input name="month4" type="checkbox" class="make-switch" data-on-color="success" data-on-text="Yes&nbsp;" data-off-text="No" {{ ($apmMonth->get(3)->selected == 1) ? 'checked' : ''}}>
                                </div>
                        </div> 

                        <div class="form-group">
                            <label class="control-label col-md-4">SO Value {{ $arMonth[4] }}</label>
                            <div class="col-md-7">
                                <input name="month5" type="checkbox" class="make-switch" data-on-color="success" data-on-text="Yes&nbsp;" data-off-text="No" {{ ($apmMonth->get(4)->selected == 1) ? 'checked' : ''}}>
                                </div>
                        </div> 

                        <div class="form-group">
                            <label class="control-label col-md-4">SO Value {{ $arMonth[5] }}</label>
                            <div class="col-md-7">
                                <input name="month6" type="checkbox" class="make-switch" data-on-color="success" data-on-text="Yes&nbsp;" data-off-text="No" {{ ($apmMonth->get(5)->selected == 1) ? 'checked' : ''}}>
                                </div>
                        </div>                                                   

                        <div class="form-group" style="padding-top: 15pt;">
                          <div class="col-sm-7 col-sm-offset-4">
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