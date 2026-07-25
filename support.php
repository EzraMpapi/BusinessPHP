<div class="space-y-4">
  <div class="section-header"><h2>Customer Support</h2>
    <button class="btn btn-primary" onclick="openTicket()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> New Ticket</button>
  </div>
  <div id="sup-kpis" class="grid-4" style="gap:12px"></div>
  <div id="ticket-table"></div>
  <div id="ticket-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md">
      <div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px] text-[#111827]">New Support Ticket</h3>
        <button onclick="document.getElementById('ticket-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Customer *</label><input id="tk-cust" class="bs-input" placeholder="Customer name"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Subject *</label><input id="tk-subject" class="bs-input" placeholder="Issue summary"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Priority</label>
          <select id="tk-priority" class="bs-input"><option>Low</option><option>Medium</option><option>High</option><option>Critical</option></select></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Description</label><textarea id="tk-desc" class="bs-input" rows="3" placeholder="Describe the issue..."></textarea></div>
        <div class="flex gap-2 pt-2"><button class="btn btn-primary flex-1" onclick="saveTicket()">Open Ticket</button>
          <button class="btn btn-secondary" onclick="document.getElementById('ticket-modal').classList.add('hidden')">Cancel</button></div>
      </div>
    </div>
  </div>
</div>
<script>
const TK_PRIORITY={Low:'#94A3B8',Medium:'#F59E0B',High:'#F97316',Critical:'#EF4444'};
const TK_STATUS={Open:'#2563EB',InProgress:'#F59E0B',Resolved:'#16A34A',Closed:'#94A3B8'};
let tickets=[
  {id:'TK-001',customer:'Mwangi Supplies',subject:'Invoice not received',priority:'High',status:'Open',date:'2026-07-22'},
  {id:'TK-002',customer:'Karibu Supermarket',subject:'Wrong items delivered',priority:'Critical',status:'InProgress',date:'2026-07-21'},
  {id:'TK-003',customer:'Salama Retailers',subject:'Payment portal error',priority:'Medium',status:'Resolved',date:'2026-07-20'},
  {id:'TK-004',customer:'Dar Tech',subject:'Account access issue',priority:'Low',status:'Closed',date:'2026-07-18'},
];
function renderSupport(){
  document.getElementById('sup-kpis').innerHTML=
    buildKpi('Open Tickets',String(tickets.filter(t=>t.status==='Open').length),'',null,'#2563EB')+
    buildKpi('In Progress',String(tickets.filter(t=>t.status==='InProgress').length),'',null,'#F59E0B')+
    buildKpi('Resolved Today',String(tickets.filter(t=>t.status==='Resolved').length),'',null,'#16A34A')+
    buildKpi('Avg Response','2.4h','-15%',true,'#7C3AED');
  document.getElementById('ticket-table').innerHTML=buildTable(
    ['Ticket #','Customer','Subject','Priority','Status','Date',''],tickets,
    t=>`<tr>
      <td class="font-mono font-semibold text-[#111827]">${t.id}</td>
      <td class="font-medium">${t.customer}</td>
      <td class="text-slate-500">${t.subject}</td>
      <td>${buildPill(t.priority,TK_PRIORITY[t.priority])}</td>
      <td>${buildPill(t.status.replace('InProgress','In Progress'),TK_STATUS[t.status])}</td>
      <td class="text-slate-500">${t.date}</td>
      <td>${t.status!=='Closed'&&t.status!=='Resolved'?`<button onclick="resolveTicket('${t.id}')" class="text-[11px] text-green-600 font-semibold hover:underline">Resolve</button>`:'—'}</td>
    </tr>`
  );lucide.createIcons();
}
function openTicket(){document.getElementById('ticket-modal').classList.remove('hidden');}
function saveTicket(){
  const cust=document.getElementById('tk-cust').value.trim();
  const subj=document.getElementById('tk-subject').value.trim();
  if(!cust||!subj){toast('Customer and subject required','error');return;}
  tickets.unshift({id:'TK-'+uid(),customer:cust,subject:subj,priority:document.getElementById('tk-priority').value,status:'Open',date:today()});
  document.getElementById('ticket-modal').classList.add('hidden');renderSupport();toast('Ticket opened');
}
function resolveTicket(id){
  tickets=tickets.map(t=>t.id===id?{...t,status:'Resolved'}:t);renderSupport();toast('Ticket resolved');
}
renderSupport();
</script>
