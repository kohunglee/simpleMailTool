# simpleMailTool
一个简单的 php 发邮件的轮子，跟其他著名大轮子相比（如 PHPMailer），特性有两个

1. 是能在不发送邮件的情况下验证账号密码是否正确
2. 用最简单和足够安全的代码，实现 99% 的人需要的全部功能（其实还能更简单，但没必要）

当然也有缺点，就是

1. 不支持 STMP 发送邮件自行生成 DKIM 签名

## DKIM 是什么意思呢？

DKIM 是一种验证邮箱是否伪造的方式。目前这类方式被广泛应用的还有 SPF、DMARC。DKIM 可以在邮箱发送时生成签名，然后在接受邮件的服务器那边利用共用的规则来进行验证，以确定这封邮件确实是邮件表头里的那个服务商地址发送的，识别伪造地址邮件和垃圾邮件（比如防止某人伪造华为的 hr@huawei.com 给别人恶作剧）。举个例子， stmp.qq.com 向 163 邮箱网站发送了一个邮件，那这个邮件里肯定包含了发送者的很多信息，像服务器的地址 @qq.com ，那么 DKIM 可以帮助 163 邮箱网站确认邮件确实是 stmp.qq.com 发送的。

## 如果不支持，会造成什么后果？

没太大后果

1. 接收方服务器会出现 DKIM 验证不通过的情况
2. QQ 邮箱后台可能会出现 “此地址未验证，请注意识别” 的字样

上面两点都是说的可能，并不是绝对，一般来讲，同服务平台间通信不会造成 DKIM 验证不通过，如 @qq.com 和 @qq.com 发邮件

不过不用担心，因为 DKIM 验证不通过很正常，DKIM 只是一种辅助手段而非唯一依据，使用本轮子，在账号密码正确情况下，其他两项都会正常通过，对于各大邮件服务提供商， DKIM 验证不通过是有其合理存在的理由的，电子邮件服务器不会拒绝由于缺少或无法验证 DKIM 签名 (RFC 4871) 的邮件。

绝大部分邮件服务提供商并不会因为 DKIM 不通过而在前台标识一些痕迹，目前我只发现 qq 邮件后台会标识，其目的或许是为了推销自己的 QQ 企业邮箱，如果想不在 QQ 邮箱后台显示，推荐配合 QQ 邮箱使用本轮子，因为同服务平台间通信不会造成 DKIM 验证不通过。

如果想彻底解决这个问题，可以使用大轮子 https://github.com/PHPMailer/PHPMailer 

## 为什么不支持自行生成 DKIM 签名？

因为追求轻量级。大轮子 PHPMailer 和本轮子发邮件的底层原理不一样，PHPMailer 是一款强大的工具，它是从几乎最底部对邮件的标头进行制作，其中包括 DKIM 签名 DKIM-Signature ，是一个驱动级别的程序，而本轮子，是利用 php 自带的 Socket 函数，与远程邮件服务器进行通信，通过发送命令的方式，轻松将邮件发送出去，原理类似于使用 Telnet 和 SMTP 通过敲命令发送邮件，如 https://blog.51cto.com/biweili/1834198 ，所以会精简很多。

利用本轮子的模式，标头中很多内容，都将由邮件服务器制作的，包括 DKIM 签名 ，而利用这种模式可能无法自定义生成 DKIM 签名，当然这只是我的猜想，目前也在找解决办法，在解决前，本轮子将不能进行自行生成 DKIM 签名。

不过，本轮子大小只有个位数 kb ，而大轮子 PHPMailer 的 kb 大小则数以百计，并且相比之下本轮子还速度更快，代码简单便于修正问题和自定义修改，又附带了不发邮件验证账号密码正确性的功能，最终生成的标头与大轮子并没有太大差别，如果仅仅为了发个简单的邮件的话，推荐使用本轮子。

# 注意

本程序还处于测试阶段，请勿用于生产环境

参考资料：

https://stackoverflow.com/questions/4712553/signing-mails-sent-through-smtp-with-dkim#comment5203489_4712553

https://stackoverflow.com/questions/2799611/setting-up-domainkeys-dkim-in-a-php-based-smtp-client

https://support.google.com/a/answer/174124?hl=zh-Hans&ref_topic=2752442
