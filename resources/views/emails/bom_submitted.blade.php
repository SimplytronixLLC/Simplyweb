@component('mail::message')
# New BOM Submission

**BOM Reference ID:** {{ $data['bom_id'] }}

---

### Customer Details

**Name:** {{ $data['name'] }}  
**Email:** {{ $data['email'] }}  
**Phone:** {{ $data['phone'] }}  
**Company:** {{ $data['company'] }}  

---

### Comments

{{ $data['comments'] ?? 'N/A' }}

---

The BOM file is attached to this email.

Thanks,  
SimplyTronix System
@endcomponent