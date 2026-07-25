<div class="space-y-4">
  <div class="section-header"><h2>Employee Portal</h2></div>
  <div class="grid-2" style="gap:16px">
    <div class="space-y-4">
      <div class="card">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 rounded-xl bg-[#16A34A] flex items-center justify-center text-white font-black text-[18px]">A</div>
          <div><h3 class="font-bold text-[15px] text-[#111827]">Amina Said</h3>
            <p class="text-[12px] text-slate-500">Sales Manager &middot; Sales Department</p></div>
        </div>
        <div class="space-y-2">
          <div class="stat"><span class="text-[12px] text-slate-500">Employee ID</span><span class="font-bold">EMP-0001</span></div>
          <div class="stat"><span class="text-[12px] text-slate-500">Monthly Salary</span><span class="font-bold text-[#16A34A]">TZS 850,000</span></div>
          <div class="stat"><span class="text-[12px] text-slate-500">Join Date</span><span class="font-semibold">March 2022</span></div>
          <div class="stat"><span class="text-[12px] text-slate-500">Contract Type</span><span class="font-semibold">Permanent</span></div>
          <div class="stat"><span class="text-[12px] text-slate-500">Leave Balance</span><span class="font-bold text-blue-600">18 days</span></div>
        </div>
      </div>
      <div class="card">
        <h3 class="font-bold text-[14px] text-[#111827] mb-3">Request Leave</h3>
        <div class="space-y-3">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Leave Type</label>
            <select id="lv-type" class="bs-input"><option>Annual Leave</option><option>Sick Leave</option><option>Emergency Leave</option><option>Maternity Leave</option></select></div>
          <div class="grid-2" style="gap:8px">
            <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">From</label><input id="lv-from" type="date" class="bs-input"/></div>
            <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">To</label><input id="lv-to" type="date" class="bs-input"/></div>
          </div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Reason</label><input id="lv-reason" class="bs-input" placeholder="Brief reason"/></div>
          <button class="btn btn-primary w-full" onclick="submitLeave()"><i data-lucide="send" class="w-3.5 h-3.5"></i> Submit Request</button>
        </div>
      </div>
    </div>
    <div class="space-y-4">
      <div class="card">
        <h3 class="font-bold text-[14px] text-[#111827] mb-3">Recent Payslips</h3>
        <div class="space-y-0">
          ${['July 2026','June 2026','May 2026','April 2026'].map((month,i)=>`
            <div class="stat">
              <div><p class="text-[12.5px] font-semibold text-[#111827]">${month}</p>
                <p class="text-[11px] text-slate-400">Gross: TZS 850,000 &middot; Net: TZS 748,000</p></div>
              <button onclick="toast('Payslip for ${month} downloaded')" class="btn btn-secondary" style="padding:4px 10px;font-size:11px"><i data-lucide="download" class="w-3 h-3"></i> Download</button>
            </div>`).join('')}
        </div>
      </div>
      <div class="card">
        <h3 class="font-bold text-[14px] text-[#111827] mb-3">Attendance This Month</h3>
        <div class="space-y-2">
          <div class="stat"><span class="text-[12px] text-slate-500">Days Present</span><span class="font-bold text-[#16A34A]">21 / 22</span></div>
          <div class="stat"><span class="text-[12px] text-slate-500">Late Arrivals</span><span class="font-bold text-orange-500">2</span></div>
          <div class="stat"><span class="text-[12px] text-slate-500">Early Departures</span><span class="font-bold">0</span></div>
          <div class="stat"><span class="text-[12px] text-slate-500">Overtime Hours</span><span class="font-bold text-blue-600">4.5 hrs</span></div>
        </div>
      </div>
      <div class="card">
        <h3 class="font-bold text-[14px] text-[#111827] mb-3">My Leave Requests</h3>
        <div id="leave-requests">
          <div class="stat"><div><p class="text-[12.5px] font-semibold">Annual Leave</p><p class="text-[11px] text-slate-400">20 - 25 Aug 2026</p></div>${buildPill('Pending','#F59E0B')}</div>
          <div class="stat"><div><p class="text-[12.5px] font-semibold">Sick Leave</p><p class="text-[11px] text-slate-400">10 Jun 2026</p></div>${buildPill('Approved','#16A34A')}</div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
function submitLeave(){
  const from=document.getElementById('lv-from').value;
  const to=document.getElementById('lv-to').value;
  const type=document.getElementById('lv-type').value;
  if(!from||!to){toast('Please select dates','error');return;}
  insertRow('hr_leave_requests',{leave_type:type,start_date:from,end_date:to,status:'Pending'}).catch(()=>{});
  toast(type+' request submitted for approval');
  document.getElementById('leave-requests').innerHTML+=
    `<div class="stat"><div><p class="text-[12.5px] font-semibold">${type}</p><p class="text-[11px] text-slate-400">${from} &mdash; ${to}</p></div>${buildPill('Pending','#F59E0B')}</div>`;
}
lucide.createIcons();
</script>