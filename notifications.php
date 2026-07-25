<div class="space-y-4">
  <div class="section-header"><h2>Notifications</h2>
    <button class="btn btn-secondary" onclick="markAllRead()"><i data-lucide="check-check" class="w-3.5 h-3.5"></i> Mark All Read</button></div>
  <div class="grid-3" style="gap:12px">
    <div id="n-unread" class="kpi"><div class="spinner"></div></div>
    <div id="n-today" class="kpi"><div class="spinner"></div></div>
    <div id="n-alerts" class="kpi"><div class="spinner"></div></div>
  </div>
  <div class="card p-0">
    <div id="notif-list"></div>
  </div>
</div>
<script>
let notifs=[
  {id:1,type:'alert',icon:'alert-circle',color:'#EF4444',title:'Low Stock Alert',message:'Cement 50kg is below reorder level (3 units remaining)',time:'2 min ago',read:false},
  {id:2,type:'payment',icon:'check-circle-2',color:'#16A34A',title:'Payment Received',message:'TZS 1,200,000 received from Salama Retailers - INV-2845',time:'18 min ago',read:false},
  {id:3,type:'invoice',icon:'file-text',color:'#2563EB',title:'Invoice Overdue',message:'Invoice INV-2844 from Dar Tech Solutions is 3 days overdue',time:'1h ago',read:false},
  {id:4,type:'approval',icon:'clipboard-check',color:'#F59E0B',title:'PO Awaiting Approval',message:'Purchase Order PO-1041 from Karibu Wholesalers needs your approval',time:'2h ago',read:true},
  {id:5,type:'hr',icon:'user',color:'#7C3AED',title:'Leave Request',message:'Fatima Omar has requested annual leave for 25-30 July 2026',time:'3h ago',read:true},
  {id:6,type:'system',icon:'settings',color:'#64748B',title:'Backup Completed',message:'Daily backup completed successfully at 02:00 AM',time:'6h ago',read:true},
  {id:7,type:'payment',icon:'check-circle-2',color:'#16A34A',title:'Payment Received',message:'TZS 5,800,000 received from Karibu Supermarket - INV-2846',time:'Yesterday',read:true},
];
function render(){
  const unread=notifs.filter(n=>!n.read).length;
  const today=notifs.filter(n=>['2 min ago','18 min ago','1h ago','2h ago','3h ago','6h ago'].includes(n.time)).length;
  const alerts=notifs.filter(n=>n.type==='alert'||n.type==='approval').length;
  document.getElementById('n-unread').innerHTML=buildKpi('Unread',String(unread),'',null,'#EF4444');
  document.getElementById('n-today').innerHTML=buildKpi('Today',String(today),'',null,'#2563EB');
  document.getElementById('n-alerts').innerHTML=buildKpi('Action Required',String(alerts),'',null,'#F59E0B');
  document.getElementById('notif-list').innerHTML=notifs.map(n=>`
    <div class="flex items-start gap-3 px-4 py-3 border-b border-slate-50 last:border-0 ${n.read?'opacity-60':''}">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:${n.color}18">
        <i data-lucide="${n.icon}" class="w-4 h-4" style="color:${n.color}"></i></div>
      <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between gap-2">
          <p class="text-[13px] font-semibold text-[#111827]">${n.title}</p>
          <span class="text-[10px] text-slate-400 flex-shrink-0">${n.time}</span></div>
        <p class="text-[12px] text-slate-500 mt-0.5">${n.message}</p>
      </div>
      ${!n.read?`<button onclick="markRead(${n.id})" class="flex-shrink-0 w-2 h-2 rounded-full mt-2" style="background:${n.color}"></button>`:''}
    </div>`).join('');
  lucide.createIcons();
}
render();
function markRead(id){notifs=notifs.map(n=>n.id==id?{...n,read:true}:n);render();toast('Marked as read');}
function markAllRead(){notifs=notifs.map(n=>({...n,read:true}));render();toast('All marked as read');}
</script>