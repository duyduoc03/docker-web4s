{assign var = website_info value = $this->Setting->getWebsiteInfo()}
{assign var = shipping_info value = $this->Shipping->getInfoShipping($id_record)}

{assign var = order_info value = []}
{if !empty($shipping_info.order_id)}
    {$order_info = $this->Order->getInfoOrder($shipping_info.order_id)}
{/if}

{assign var = plugins value = $this->Setting->getListPlugins()}

{if !empty($shipping_info)}
    {assign var = date value = ""}
    {assign var = month value = ""}
    {assign var = year value = ""}
    {if !empty($shipping_info.created)}
        {assign var = date value = $this->Utilities->convertIntgerToDateString($shipping_info.created, 'd')}
        {assign var = month value = $this->Utilities->convertIntgerToDateString($shipping_info.created, 'm')}
        {assign var = year value = $this->Utilities->convertIntgerToDateString($shipping_info.created, 'Y')}
    {/if}

    {assign var = contact value = ""}
    {if !empty($order_info.contact)}
        {assign var = contact value = $order_info.contact}
    {/if}

    {assign var = items value = ""}
    {if !empty($order_info.items)}
        {assign var = items value = $order_info.items}
    {/if}

    <div class="container-print">
        <div class="page">
            <div class="page-content">
                {*Thông tin hóa đơn*}
                <table class="w-100 border-bottom border-black">
                    <tbody>
                        <tr>
                            <td class="w-25">
                                <img class="logo img-fluid" style="max-height: 50px" src="{if !empty($website_info.company_logo)}{CDN_URL}{$website_info.company_logo}{/if}" />
                            </td>
                            <td class="text-center">
                                <div class="mt-2 mb-3">
                                    <div class="font-weight-bold h4 mb-0">VẬN ĐƠN</div>
                                    <div class="h4 mb-0">SHIPMENT</div>
                                </div>
                                <div class="mb-2">
                                    <span>Ngày <i>(date)</i></span>
                                    <span class="font-weight-bold px-1">{$date}</span>
                                    <span>Tháng <i>(month)</i></span>
                                    <span class="font-weight-bold px-1">{$month}</span>
                                    <span>Năm <i>(year)</i></span>
                                    <span class="font-weight-bold px-1">{$year}</span>
                                </div>
                            </td>
                            <td class="w-25">
                                <div>
                                    <div>Mã vận đơn <i>(No.): </i>
                                        <span class="font-weight-bold text-danger h5">
                                            {if !empty($shipping_info.code)}{$shipping_info.code}{/if}
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                {*Thông tin người nhận*}
                <table class="w-100 border-bottom border-black">
                    <tbody>
                        <tr>
                            <td class="pt-2 pl-1 pr-1 pb-3">
                                <div class="row mx-n1">
                                    <div class="col-4 px-1">
                                        <span>Họ tên người nhận <i>(Receiver)</i></span>
                                    </div>
                                    <div class="col-8 px-1">
                                        <div class="d-flex">
                                            <div class="pr-1">:</div>
                                            <div class="dotted-line">
                                                {if !empty($contact.full_name)}
                                                    {$contact.full_name}
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mx-n1">
                                    <div class="col-4 px-1">
                                        <span>Số điện thoại <i>(Tel)</i></span>
                                    </div>
                                    <div class="col-8 px-1">
                                        <div class="d-flex">
                                            <div class="pr-1">:</div>
                                            <div class="dotted-line">
                                                {if !empty($contact.phone)}
                                                    {$contact.phone}
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mx-n1">
                                    <div class="col-4 px-1">
                                        <span>Email</span>
                                    </div>
                                    <div class="col-8 px-1">
                                        <div class="d-flex">
                                            <div class="pr-1">:</div>
                                            <div class="dotted-line">
                                                {if !empty($contact.email)}
                                                    {$contact.email}
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mx-n1">
                                    <div class="col-4 px-1">
                                        <span>Địa chỉ <i>(Address)</i></span>
                                    </div>
                                    <div class="col-8 px-1">
                                        <div class="d-flex">
                                            <div class="pr-1">:</div>
                                            <div class="dotted-line">
                                                {if !empty($contact.full_address)}
                                                    {$contact.full_address}
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mx-n1">
                                    <div class="col-4 px-1">
                                        <span>Phương thức nhận hàng <i>(Delivery method)</i></span>
                                    </div>
                                    <div class="col-8 px-1">
                                        <div class="d-flex">
                                            <div class="pr-1">:</div>
                                            <div class="dotted-line">
                                                {if isset($shipping_info.shipping_method) && $shipping_info.shipping_method == {RECEIVED_AT_STORE}}
                                                    {__d('admin', 'nhan_tai_cua_hang')}
                                                {/if}

                                                {if isset($shipping_info.shipping_method) && $shipping_info.shipping_method == {NORMAL_SHIPPING}}
                                                    {__d('admin', 'van_chuyen_thong_thuong')}
                                                {/if}

                                                {if isset($shipping_info.shipping_method) && $shipping_info.shipping_method == {SHIPPING_CARRIER}}
                                                    {__d('admin', 'gui_hang_van_chuyen')}
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {if !empty($shipping_info.cod_money)}
                                    <div class="row mx-n1">
                                        <div class="col-4 px-1">
                                            <span>Tiền thu hộ <i>(COD)</i></span>
                                        </div>
                                        <div class="col-8 px-1">
                                            <div class="d-flex">
                                                <div class="pr-1">:</div>
                                                <div class="dotted-line">
                                                    {$shipping_info.cod_money|number_format:0:".":","}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                {/if}
                            </td>
                        </tr>
                    </tbody>
                </table>

                {*Thông tin đơn hàng*}
                <div class="border-bottom border-black">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 25px;">
                                    <span class="font-weight-bold">STT</span><br />
                                    <small>(No.)</small>
                                </th>
                                <th style="width: 190px;">
                                    <span class="font-weight-bold">Tên hàng hóa, dịch vụ</span><br />
                                    <small>(Description)</small>
                                </th>
                                <th style="width: 50px;">
                                    <span class="font-weight-bold">Số lượng</span><br />
                                    <small>(Quantity)</small>
                                </th>
                                <th style="width: 90px;">
                                    <span class="font-weight-bold">Đơn giá</span><br />
                                    <small>(Unit price)</small>
                                </th>
                                <th style="width: 90px;">
                                    <span class="font-weight-bold">Tiền thuế</span><br />
                                    <small>(VAT)</small>
                                </th>
                                <th style="width: 135px;">
                                    <span class="font-weight-bold">Thành tiền (Gồm thuế GTGT)</span><br />
                                    <small>(Amount)</small>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {if !empty($items)}
                                {foreach from = $items item = item}
                                    <tr class="item">
                                        <td class="text-center item--product">
                                            {$item@iteration}
                                        </td>
                                        <td class="item--product">
                                            {if !empty($item.name_extend)}
                                                {$item.name_extend}
                                            {/if}
                                        </td>
                                        <td class="text-right item--product">
                                            {if !empty($item.quantity)}
                                                {$item.quantity|number_format:0:".":","}
                                            {/if}
                                        </td>
                                        <td class="text-right item--product">
                                            {if !empty($item.price)}
                                                {$item.price|number_format:0:".":","}
                                            {/if}
                                        </td>
                                        <td class="text-right item--product" style="width: 110px;">
                                            {if !empty($item.total_vat)}
                                                {$item.total_vat|number_format:0:".":","}
                                            {/if}
                                        </td>
                                        <td class="text-right item--product" style="width: 130px;">
                                            {if !empty($item.total_item)}
                                                {$item.total_item|number_format:0:".":","}
                                            {/if}
                                        </td>
                                    </tr>
                                {/foreach}
                            {/if}
                            {if !empty($order_info.total_items)}
                                <tr>
                                    <td colspan="5">
                                        <div>
                                            <div class="text-right">
                                                <span>Đơn giá <i>(Total Items)</i>:</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {$order_info.total_items|number_format:0:".":","}
                                    </td>
                                </tr>
                            {/if}
                            {if !empty($order_info.shipping_fee_customer)}
                                <tr>
                                    <td colspan="5">
                                        <div>
                                            <div class="text-right">
                                                <span>Phí vận chuyển <i>(Shipping)</i>:</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {$order_info.shipping_fee_customer|number_format:0:".":","}
                                    </td>
                                </tr>
                            {/if}
                            {if !empty($plugins.promotion) && !empty($order_info.total_coupon)}
                                <tr>
                                    <td colspan="5">
                                        <div>
                                            <div class="text-right">
                                                <span>Phiếu giảm giá <i>(Coupon)</i>:</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {$order_info.total_coupon|number_format:0:".":","}
                                    </td>
                                </tr>
                            {/if}
                            {if !empty($plugins.affiliate) && !empty($order_info.total_affiliate)}
                                <tr>
                                    <td colspan="5">
                                        <div>
                                            <div class="text-right">
                                                <span>Mã giới thiệu <i>(Affiliate)</i>:</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {$order_info.total_affiliate|number_format:0:".":","}
                                    </td>
                                </tr>
                            {/if}
                            {if !empty($order_info.total_vat)}
                                <tr>
                                    <td colspan="5">
                                        <div>
                                            <div class="text-right">
                                                <span>VAT</i>:</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {$order_info.total_vat|number_format:0:".":","}
                                    </td>
                                </tr>
                            {/if}
                            {if !empty($order_info.total)}
                                <tr>
                                    <td colspan="5">
                                        <div>
                                            <div class="text-right">
                                                <span>Cộng tiền hàng <i>(Total amount)</i>:</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {$order_info.total|number_format:0:".":","}
                                    </td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>

                {*Chữ ký*}
                <div class="row text-center justify-content-around mt-3" style="margin-bottom: 100px">
                    <div class="col-4">
                        <div class="font-weight-bold">Người nhận hàng <i>(Receiver)</i></div>
                        <div class="font-weight-bold">Ký, ghi rõ họ tên</div>
                        <i>(Sign &amp; full name)</i>
                    </div>
                    <div class="col-4">
                        <div class="font-weight-bold">Người bán hàng <i>(Seller)</i></div>
                        <div class="font-weight-bold">Ký, đóng dấu, ghi rõ họ tên</div>
                        <i>(Sign, stamp &amp; full name)</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}