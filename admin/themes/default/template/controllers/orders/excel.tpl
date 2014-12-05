<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <mata name="Google web spider" content="notfollow">
    <mata name="Google web spider" content="noarchive">
    <mata name="Google spider" content="notfollow">
    <mata name="Google spider" content="noarchive">
</head>
<body>
    <table width="95%" border="1" cellspacing="0" cellpadding="0">
        <tr>
            <td>訂單編號</td>
            <td>訂單狀態</td>
            <td>付款時間</td>
            <td>產品名稱</td>
            <td>數量</td>
            <td>單價</td>
            <td>運費</td>
            <td>小計[(單價＋運費）Ｘ數量]</td>
            <td>使用紅利點數</td>
            <td>應付款金額</td>
            <td>訂購者姓名</td>
            <td>訂購者電話</td>
            <td>訂購者Email</td>
            <td>收件人姓名</td>
            <td>收件人電話</td>
            <td>收件地址</td>
            <td>收件時間</td>
            <td>會員備註</td>
        </tr>
        {foreach $list as $item}
        <tr>
            <td>{$item.id_order}</td>
            <td>{$item.date_add}</td>
            <td></td>
            <td>{$item.product_name}</td>
            <td>{$item.product_quantity}</td>
            <td>{$item.product_unit_price}</td>
            <td></td>
            <td>{$item.product_total_price}</td>
            <td></td>
            <td>{$item.total_paid_tax_incl}</td>
            <td>{$item.customer}</td>
            <td></td>
            <td>{$item.customer_email}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        {/foreach}
    </table>
</body>
</html>