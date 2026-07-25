<div class="space-y-4">
  <div class="section-header"><h2>Workflow Automation</h2>
    <button class="btn btn-primary" onclick="openWFForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> New Workflow</button></div>
  <div class="grid-3" style="gap:12px">
    <div id="wf-kpis"></div>
    <div id="wf-kpis2"></div>
    <div id="wf-kpis3"></div>
  </div>
  <div id="wf-table"></div>
  <div id="wf-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md"><div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px]">New Workflow</h3>
      <button onclick="document.getElementById('wf-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Workflow Name *</label><input id="wf-name" class="bs-input" placeholder="e.g. Invoice Approval"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Trigger</label>
          <select id="wf-trigger" class="bs-input"><option>Invoice Created</option><option>PO Submitted</option><option>Lead Won</option><option>Low Stock</option><option>Payment Received</option><option>Leave Requested</option></select></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Action</label>
          <select id="wf-action" class="bs-input"><option>Send Email</option><option>Send SMS</option><option>Send WhatsApp</option><option>Create Task</option><option>Notify Admin</option></select></div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveWorkflow()">Create</button>
          <button class="btn btn-secondary" onclick="document.getElementById('wf-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let workflows=[
  {id:1,name:'Invoice Overdue Alert',trigger:'Invoice Created',action:'Send Email',runs:142,status:'Active',last:'2026-07-22'},
  {id:2,name:'Low Stock SMS Alert',trigger:'Low Stock',action:'Send SMS',runs:28,status:'Active',last:'2026-07-21'},
  {id:3,name:'PO Approval Chain',trigger:'PO Submitted',action:'Notify Admin',runs:55,status:'Active',last:'2026-07-20'},
  {id:4,name:'Welcome New Lead',trigger:'Lead Won',action:'Send WhatsApp',runs:89,status:'Paused',last:'2026-07-15'},
  {id:5,name:'Payment Confirmation',trigger:'Payment Received',action:'Send Email',runs:201,status:'Active',last:'2026-07-22'},
];
const WFS={Active:'#16A34A',Paused:'#F59E0B',Inactive:'#EF4444'};
function render(){
  const active=workflows.filter(w=>w.status==='Active').length;
  const totalRuns=workflows.reduce((s,w)=>s+(w.runs||0),0);
  document.getElementById('wf-kpis').innerHTML=buildKpi('Active Workflows',String(active),'',null,'#16A34A');
  document.getElementById('wf-kpis2').innerHTML=buildKpi('Total Runs',String(totalRuns),'+12',true,'#2563EB');
  document.getElementById('wf-kpis3').innerHTML=buildKpi('Automations Built',String(workflows.length),'',null,'#7C3AED');
  document.getElementById('wf-table').innerHTML=buildTable(
    ['Workflow','Trigger','Action','Runs','Status','Last Run',''],
    workflows,w=>`<tr>
      <td class="font-semibold text-[#111827]">${w.name}</td>
      <td><span class="pill bg-blue-50 text-blue-600">${w.trigger}</span></td>
      <td><span class="pill bg-purple-50 text-purple-600">${w.action}</span></td>
      <td class="font-bold">${w.runs||0}</td>
      <td>${buildPill(w.status,WFS[w.status]||'#94A3B8')}</td>
      <td class="text-slate-500">${w.last||''}</td>
      <td><button onclick="toggleWF(${w.id})" class="text-[11px] ${w.status==='Active'?'text-orange-500':'text-green-600'} font-semibold">${w.status==='Active'?'Pause':'Activate'}</button></td>
    </tr>`);
  lucide.createIcons();
}
render();
function toggleWF(id){
  workflows=workflows.map(w=>w.id==id?{...w,status:w.status==='Active'?'Paused':'Active'}:w);
  render();toast('Workflow updated');
}
function openWFForm(){document.getElementById('wf-modal').classList.remove('hidden');}
async function saveWorkflow(){
  const name=document.getElementById('wf-name').value.trim();
  if(!name){toast('Name required','error');return;}
  workflows.unshift({id:uid(),name,trigger:document.getElementById('wf-trigger').value,
    action:document.getElementById('wf-action').value,runs:0,status:'Active',last:today()});
  try{await insertRow('workflows',{name,trigger:document.getElementById('wf-trigger').value,action:document.getElementById('wf-action').value,status:'Active'});toast('Workflow created!');}catch(e){toast('Added locally');}
  document.getElementById('wf-modal').classList.add('hidden');render();
}
</script>