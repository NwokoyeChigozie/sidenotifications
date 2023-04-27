package names

type NotificationName string

const (
	SendWelcomeMail            NotificationName = "send_welcome_mail"
	SendWelcomeSMS             NotificationName = "send_welcome_sms"
	SendOTP                    NotificationName = "send_otp"
	SendWelcomePasswordMail    NotificationName = "send_welcome_password_mail"
	SendResetPasswordMail      NotificationName = "send_reset_password_mail"
	SendResetPasswordSMS       NotificationName = "send_reset_password_sms"
	SendResetPasswordDoneMail  NotificationName = "send_reset_password_done_mail"
	SendResetPasswordDoneSMS   NotificationName = "send_reset_password_done_sms"
	SendEmailVerificationMail  NotificationName = "send_email_verification_mail"
	SendEmailVerifiedMail      NotificationName = "send_email_verified_mail"
	SendSMSToPhone             NotificationName = "send_sms_to_phone"
	SendVerificationFailed     NotificationName = "send_verification_failed"
	SendVerificationSuccessful NotificationName = "send_verification_successful"
	SendAuthorized             NotificationName = "send_authorized"
	SendAuthorization          NotificationName = "send_authorization"
)
