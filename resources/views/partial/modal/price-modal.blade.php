<!-- BEGIN MODAL POPUP -->
<div id="price" class="modal container fade" tabindex="false" data-width="760" role="dialog">
    <div class="modal-header" style="margin-top: 30px;margin-left: 30px;margin-right: 30px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><b><span id="title">ADD NEW</span> PRICE</b></h4>
    </div>
    <div class="modal-body" style="margin-bottom: 30px;margin-left: 30px;margin-right: 30px;">

        <form id="form_price" class="form-horizontal" action="{{ url('price') }}" method="POST">
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
                          <label class="col-sm-3 control-label">Product</label>
                          <div class="col-sm-8">

                          <div class="input-group" style="width: 100%;">

                                <select class="select2select" name="product_id" id="product" required></select>

                                <span class="input-group-addon display-hide">
                                    <i class="fa"></i>
                                </span>

                            </div>

                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Global Channel</label>
                          <div class="col-sm-8">

                          <div class="input-group" style="width: 100%;">

                                <select class="select2select" name="globalchannel_id" id="globalchannel" required></select>

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
                          <label class="col-sm-3 control-label">Price</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" id="prices" name="price" class="form-control" placeholder="Input Price" data-tooltip="true" />
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Release Date</label>
                          <div class="col-sm-8">
                            <div class="input-icon right">
                              <i class="fa"></i>
                              <input type="text" id="release_date" name="release_date" class="form-control" placeholder="Release Date" />
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