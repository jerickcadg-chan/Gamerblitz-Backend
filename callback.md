## QR Callback

```JSON
{
    "event": "qr.payment",
    "api_version": "2022-07-31",
    "business_id": "58cd618ba0464eb64acdb246",
    "created": "2022-10-22T06:30:05.86474Z", 
    "data": {
        "id": "qrpy_8182837te-87st-49ing-8696-1239bd4d759c",
        "business_id": "58cd618ba0464eb64acdb246",
        "currency": "IDR",
        "amount": 10000,
        "status": "SUCCEEDED",
        "created": "2022-10-22T06:30:05.86474Z",
        "qr_id": "qr_61cb3576-3a25-4d35-8d15-0e8e3bdba4f2",
        "qr_string": "0002010102##########CO.XENDIT.WWW011893600#######14220002152#####414220010303TTT####015CO.XENDIT.WWW02180000000000000000000TTT52045######ID5911XenditQRIS6007Jakarta6105121606##########3k1mOnF73h11111111#3k1mOnF73h6v53033605401163040BDB",
        "reference_id": "order-id-1666420204",
        "type": "DYNAMIC",
        "channel_code": "ID_DANA",
        "expires_at": "2022-10-23T09:56:43.60445Z",
        "description": "",
        "basket": null,
        "metadata": null,
        "payment_detail": {
            "receipt_id": "000111666",
            "source": "GOPAY",
            "name": null,
            "account_details": null
        }
    }
}
```

## VA Callback

```JSON
{
    "id": "598d91b1191029596846047f",
    "payment_id": "5f218745736e619164dc8608",
    "callback_virtual_account_id": "598d5f71bf64853820c49a18",
    "owner_id": "57b4e5181473eeb61c11f9b9",
    "external_id": "demo-1502437214715",
    "account_number": "999939380502",
    "bank_code": "BNC",
    "transaction_timestamp": "2021-07-24T05:22:55.115Z",
    "amount": 50000,
    "merchant_code": "90100010",
    "currency": "IDR",
    "country" : "ID",
    "sender_name": "Michael Chen",
    "payment_detail": {
        "payment_interface": "MOBILE_BANKING",
        "remark": "Sent by Michael for my package",
        "reference": "66143641700",
        "sender_account_number": "12345678912345",
        "sender_channel_code": "BNC",
        "sender_name": "Michael Chen",
        "transfer_method": "INHOUSE"
    }
}
```
