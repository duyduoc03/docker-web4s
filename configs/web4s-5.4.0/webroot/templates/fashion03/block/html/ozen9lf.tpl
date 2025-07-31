{strip}<div class="modal fade form-contact-phone" id="formContactPhone" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <i class="fa-sharp fa-light fa-circle-xmark"></i>
            </button>
            <div class="modal-body">
                <div class="form">
                    <div class="inter-form">
                        <i class="fa-solid fa-calendar-days"></i>
                        <div class="title">
                    		Xin chào,
                    	</div>
                    	<div class="slogan">
                    	    Vui lòng nhập thông tin để chúng tôi liên hệ lại với bạn theo lịch hẹn.
                    	</div>
                        <form nh-form-contact="3HZO5VFWIK" action="/contact/send-info" method="POST"  autocomplete="off">
                            <div class="form-group">
                                <input name="full_name" type="text" class="form-control" required data-msg="{__d('template', 'vui_long_nhap_thong_tin')}" placeholder="Họ và tên*">
                            </div>
                            
                            <div class="form-group">
                                <input name="phone" type="text" class="form-control" required data-msg="{__d('template', 'vui_long_nhap_thong_tin')}" data-rule-phoneVN data-msg-phoneVN="{__d('template', 'so_dien_thoai_chua_chinh_xac')}" placeholder="Số điện thoại liên hệ lại*">
                            </div>
                            
                            <div class="form-group">
                                <input name="email" type="email" class="form-control" required data-msg="{__d('template', 'vui_long_nhap_thong_tin')}"  placeholder="Email liên hệ lại*">
                            </div>
         
                            <div class="form-group">
                                <textarea name="content" maxlength="500" class="form-control" required data-msg="{__d('template', 'vui_long_nhap_thong_tin')}" placeholder="Nội dung?"></textarea>
                            </div>
                           
                            <div class="form-group mb-0">
                                <span nh-btn-action="submit" class="btn btn-submit ">
                                    Gửi yêu cầu
                                </span>
                                <div class="go-hotline">
                                    Gọi hotline <a href="tel:0944681533">0944.681.533</a> (24/7)
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>{/strip}