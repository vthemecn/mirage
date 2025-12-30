# 用户信息更新API接口文档

## 接口信息

- **接口路径**: `/wp-json/vtheme/v1/users/{id}`
- **请求方法**: `POST`
- **内容类型**: `application/json`

## 功能说明

用于更新指定用户的基本信息，包括昵称、邮箱、性别、手机号、个人简介等。

## 请求参数

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 用户ID，路径参数 |
| nickname | string | 否 | 用户昵称 |
| email | string | 否 | 用户邮箱 |
| gender | string | 否 | 性别，0-保密，1-男，2-女 |
| mobile | string | 否 | 手机号 |
| description | string | 否 | 个人简介 |

## 请求头

| 参数名 | 值 | 说明 |
|--------|----|----|
| Content-Type | application/json | 请求内容类型 |
| X-WP-Nonce | string | WordPress安全验证令牌 |

## 请求示例

```json
{
  "nickname": "新昵称",
  "email": "user@example.com",
  "gender": "1",
  "mobile": "13800138000",
  "description": "这是我的个人简介"
}
```

## 响应格式

成功时返回用户更新后的信息：

```json
{
  "id": 1,
  "display_name": "新昵称",
  "user_email": "user@example.com",
  "description": "这是我的个人简介",
  "gender": "1",
  "mobile": "13800138000"
}
```

## 响应码

| 状态码 | 说明 |
|--------|------|
| 200 | 更新成功 |
| 401 | 没有权限修改此用户 |
| 404 | 用户不存在 |
| 500 | 更新失败 |

## 权限说明

- 只能修改自己的用户信息（用户ID与当前登录用户ID相同）
- 管理员可以修改任意用户信息
- 非管理员不能修改其他用户的资料

## 注意事项

- 邮箱需要符合邮箱格式验证
- 性别值只能是'0'、'1'或'2'
- 所有输入都会经过WordPress的安全过滤