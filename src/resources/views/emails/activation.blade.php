@if(isset($custom))
    <div>{!! $custom !!}</div>
@endif

@if(isset($ocEmail))
    <div style="padding-bottom: 20px">{!! trans('HCNewCore::users.activation.mail.email', ['email' => $ocEmail]) !!}</div>
@endif

---
<div style="padding-bottom: 20px">{!! trans('HCNewCore::users.activation.mail.link', ['link' => $link]) !!}</div>
