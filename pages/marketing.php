<div class="space-y-4">
  <div class="section-header"><h2>Marketing Campaigns</h2>
    <button class="btn btn-primary" onclick="openCampForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> New Campaign</button></div>
  <div id="mkt-kpis" class="grid-3" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div></div>
  <div id="camp-table"></div>
  <div id="camp-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md"><div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px]">New Campaign</h3>
      <button onclick="document.getElementById('camp-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Campaign Name *</label><input id="c-name" class="bs-input" placeholder="e.g. Ramadan Sale 2026"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Channel</label>
          <select id="c-channel" class="bs-input"><option>Email</option><option>SMS</option><option>WhatsApp</option><option>Social Media</option><option>Radio</option></select></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Budget (<?= CURRENCY ?>)</label><input id="c-budget" type="number" class="bs-input" placeholder="0"/></div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Start Date</label><input id="c-start" type="date" class="bs-input"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">End Date</label><input id="c-end" type="date" class="bs-input"/></div>
        </div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveCampaign()">Create</button>
          <button class="btn btn-secondary" onclick="document.getElementById('camp-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let campaigns=[];
const CAMP_COLOR={Active:'#16A34A',Draft:'#94A3B8',Completed:'#2563EB',Paused:'#F59E0B'};
(async()=>{
  const rows=await fetchRows('marketing_campaigns');
  campaigns=rows.length?rows:[
    {id:'CAM-041',name:'July Independence Sale',channel:'SMS',budget:500000,spent:320000,leads:145,status:'Active',start:'2026-07-01',end:'2026-07-31'},
    {id:'CAM-040',name:'New Product Launch',channel:'WhatsApp',budget:800000,spent:800000,leads:312,status:'Completed',start:'2026-06-01',end:'2026-06-30'},
    {id:'CAM-039',name:'Customer Win-Back',channel:'Email',budget:200000,spent:50000,leads:28,status:'Active',start:'2026-07-15',end:'2026-08-15'},
    {id:'CAM-038',name:'Seasonal Discount',channel:'Social Media',budget:1200000,spent:0,leads:0,status:'Draft',start:'2026-08-01',end:'2026-08-31'},
  ];render();
})();
function render(){
  const active=campaigns.filter(c=>c.status==='Active').length;
  const totalBudget=campaigns.reduce((s,c)=>s+(c.budget||0),0);
  const totalLeads=campaigns.reduce((s,c)=>s+(c.leads||0),0);
  document.getElementById('mkt-kpis').innerHTML=
    buildKpi('Active Campaigns',String(active),'',null,'#16A34A')+
    buildKpi('Total Budget',money(totalBudget),'',null,'#2563EB')+
    buildKpi('Leads Generated',String(totalLeads),'+45',true,'#7C3AED');
  document.getElementById('camp-table').innerHTML=buildTable(
    ['ID','Campaign','Channel','Budget','Spent','Leads','Status','Period'],
    campaigns,c=>`<tr>
      <td class="font-mono text-[11px] text-slate-500">${c.id||c.campaign_id}</td>
      <td class="font-semibold text-[#111827]">${c.name||c.campaign_name}</td>
      <td><span class="pill bg-slate-50 text-slate-600">${c.channel}</span></td>
      <td class="font-bold">${money(c.budget)}</td>
      <td class="text-slate-500">${money(c.spent||0)}</td>
      <td class="font-semibold text-[#111827]">${(c.leads||0).toLocaleString()}</td>
      <td>${buildPill(c.status,CAMP_COLOR[c.status]||'#94A3B8')}</td>
      <td class="text-slate-500 text-[11px]">${c.start||''} &mdash; ${c.end||''}</td>
    </tr>`);
  lucide.createIcons();
}
function openCampForm(){document.getElementById('camp-modal').classList.remove('hidden');}
async function saveCampaign(){
  const name=document.getElementById('c-name').value.trim();
  if(!name){toast('Name required','error');return;}
  const row={id:'CAM-'+uid(),name,channel:document.getElementById('c-channel').value,
    budget:Number(document.getElementById('c-budget').value)||0,spent:0,leads:0,
    status:'Draft',start:document.getElementById('c-start').value,end:document.getElementById('c-end').value};
  campaigns.unshift(row);
  try{await insertRow('marketing_campaigns',{campaign_name:row.name,...row});toast('Campaign created!');}catch(e){toast('Added locally');}
  document.getElementById('camp-modal').classList.add('hidden');render();
}
</script>