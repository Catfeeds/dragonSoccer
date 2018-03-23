<?php

return [
    'alipay' => [
        // 支付宝分配的 APPID
        'app_id' => '2017111609966863',

        // 支付宝异步通知地址
        'notify_url' => PHP_SAPI==='cli'?false:url('alinotify'),

        // 支付成功后同步通知地址
        'return_url' => '',

        // 阿里公共密钥，验证签名时使用
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAl3Z7LPNFfn8A+y3bQVepmFd8NpDcyNnGTVwloEaGpifMy0qKxx037dpMvzhFGd7WxhxfouT8DSq9JAIbWvfWphwzyjcm4I2beaSoWuzNucUHWaz9Q0rbbuqy937CjqSsbLwKxSQX0cBsEKrpQ38LJFJkP96fUYJadhTyDJmZMTv35Wy4n8U9KDR5+zZ/7DajpnJLFRrDMFNsxS9AbLZJJBNgURs4J2vQ9/m1JOz4P3lZwuuw/aeePbNxZwXw8eSScJaHYitNeQ7+JN4D/HRUzWJ/kswrrzgq8FMRmW6/YcX9JiAf3pw2xFkaANliwBhYPXDZOZ4Ec4E8dnKA82e3XwIDAQAB',        

        // 自己的私钥，签名时使用
        'private_key' => 'MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQC6Sw0A6YtVSH2SvtanFSkowp9fGEyoHK+TPVL2iHG64t8eMEG8WZrvGXzu4MqoqhGECwcK/9EdwkdlM33N9hRMPiww1amEWo3jyb5WKUFOmF202pKhm1YRAwBgrU9RkNavGyDt2m6S4RQsUjifH+h54JZPdJBYe4nEKogbNcJVp2g4m0/ZGMtrKKfzqXxA05piw25+xA8BSdQ+q57fr8x3yK6qZUtKHln2dKq+ux+Zcc7R4BL4j7g8YkCK7ZetXeHbm3i9inZj7O0cfrfse6DaZErKchOFO0Gh/WnMsHkRujikrq+Lp+cVDsK8Pxg8pXNJMQBArYtCkJZi+lruHXwNAgMBAAECggEBAID+3u42ySgwneMzCeeAG5QBz+FFLi9qTZki6YOUT0wdNGnu+FW3pjTR7VRri2jm+mH4UNQo92An2tAUq5QmRT/V/TuDd3ISUhXc6FM5FeOaVaiZgNcufJYjAevvlfDg3gc6Pb71dYN3H0ThWhu1OIDMJsi16g2a3XZcDQrqWrp7lntbx5r43RCzP/wNrJzeB+fz3mmeqMyBe0oLI1zyMIAveiItd/RgOW3wyaXp6WB6DGoTSzfBCsru2nHQYyMt6DPOh8AqhcW+c5ey9N8AhfiEq763dSlaQpnwaFl7mIZpoyfek36HAPI6bkHhQaaqW9x/CAlzKjp+zemfRNPvsvECgYEA4MmKEPvPAqVUp1n9IO6hrEX81KtTJizwyXDUMVxuD4bxlmnW16ivvRed4J3heRsEISdUYmSnd5b5u7d+hb1y4lLc9ROJA7UKjU/DbcpUPwjIdgA3G/sHBK6GV4MSo6JrOEYURtVm7c+j9EcrxWt30t3RHW0pMGOybs4vxbUW5VsCgYEA1CkruFyHlvC+uDz9Syw5zQshl5suMZVyP5vecARXqb9D2FDw3kMtUnBZJmGjBkTSvfUaIqY0u8iT7RhU2BsC4B0vF/f6PSKHVuWhBUyRdGgOqb1IU3zZTSbOXM8iTTLSeph+xT6q8SQMDaM5Qgd4NlkJE1DTMuvQdJITrgDtGLcCgYEAkI7LbXSigg8Uy7LBaIZODl3L5HBxPqG0D7exnjTUysN7ZcGW2oWuzqn7a9HciGdpVnDWgZg9YlkDLUcp5JQa55VmZ20yteGdcZcUFO0DFK5vTAODbUkYEFi0KF8wg28WCNB4hb0DPnhD9fo3GfSs0Dy6GHR1Apt6ymtqqXh1yvUCgYBd0bsAswJOsDVWmbnU5UDGOJbpSPk7ef/kuxO5a2IssWTaIqjxULmZDA+QVnrWCdc7o0ika+VD6SXpIepbCk7SNnWd3s3s/PjmZ3M2Oa7U0DMzwn1aVgpuAKwfBIYBp2jGR+s/ZEAJlMwzt6tZVJ5HQkYuIX7TtM4gznwfZU9zrwKBgQDBWr1hkKRPQMWOBkURIcdVDNuZ8UYVIqYymOHpSBdvn41HhytQD6NSqKMyXVHdppiXKFgKjClds3b5i2sJNKBflzSc4yAY7xJcJJFBoTcPIGTFzNFqzJ+DJMHAv+7wwIom1MJUMRszxRjF0UgMG3UYNwn9c94jNJTQoiTAVbYO5Q==',
    ],

    'wechat' => [
        // 公众号APPID
        'app_id' => '',

        // 小程序APPID
        'miniapp_id' => '',

        // APP 引用的 appid
        'appid' => 'wx49c3b40c7298404e',

        // 微信支付分配的微信商户号
        'mch_id' => '1491982012',

        // 微信支付异步通知地址
        'notify_url' => PHP_SAPI==='cli'?false:url('wechatnotify'),

        // 微信支付签名秘钥
        'key' => 'IjTdbBcoVDaXEm8Mm31qoSMSQAiVSeqv',

        // 客户端证书路径，退款时需要用到。请填写绝对路径，linux 请确保权限问题。pem 格式。
        'cert_client' => '',

        // 客户端秘钥路径，退款时需要用到。请填写绝对路径，linux 请确保权限问题。pem 格式。
        'cert_key' => '',
    ],
];
