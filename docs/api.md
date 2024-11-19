MirageV 主题接口
======

## 微信小程序登录接口

**Method** : `POST`

**URL** : `/wp-json/vtheme/v1/wxapp-login`

**Auth required** : `False`

**Body** :

```json
{ "code": "......" }
```

code: 微信小程序授权登录后获取的code

### Success Response

**Code** : `200 OK`

**Content example** :

```json
{
  "message": "登录成功",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzE5ODM3NjEsImV4cCI6MTczMjA0Mzc2MSwibmJmIjoxNzMxOTgzNzYxLCJ1c2VyX2lkIjoiNSJ9.KeC54kIkB7cfle-Cx1BVbRREJkOfIZUUQS-i1mV0nbI",
  "user": {
    "ID": "5",
    "user_login": "u_6738ae282114b44",
    "user_nicename": "u_6738ae282114b44",
    "user_email": "",
    "user_url": "",
    "user_registered": "2024-11-16 14:37:28",
    "user_activation_key": "",
    "user_status": "0",
    "display_name": "微信用户",
    "id": "5",
    "avatar": "",
    "nickname": "微信用户",
    "mobile": "",
    "gender": "",
    "address": "",
    "dob": "",
    "description": "",
    "ip": "",
    "updated_at": ""
  }
}
```


## 微信小程序设置信息接口

**Method** : `GET`

**URL** : `/wp-json/vtheme/v1/index`

**Auth required** : `False`


### Success Response

**Code** : `200 OK`

**Content example** :

```json
{
  "app_lastest_ids": "",
  "app_hot_ids": "11",
  "app_about_id": "7",
  "app_using_id": "814",
  "app_privacy_id": "3"
}
```
















