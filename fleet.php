<div class="space-y-4">
  <div class="section-header"><h2>Fleet Management</h2>
    <button class="btn btn-primary" onclick="openVehicleForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Vehicle</button></div>
  <div id="flt-kpis" class="grid-4" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div></div>
  <div id="fleet-table"></div>
  <div id="veh-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md"><div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px]">Add Vehicle</h3>
      <button onclick="document.getElementById('veh-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Plate Number *</label><input id="v-plate" class="bs-input" placeholder="T 123 ABC"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Type</label>
            <select id="v-type" class="bs-input"><option>Truck</option><option>Van</option><option>Pickup</option><option>Car</option><option>Motorcycle</option></select></div>
        </div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Make & Model</label><input id="v-model" class="bs-input" placeholder="e.g. Toyota Hiace"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Year</label><input id="v-year" type="number" class="bs-input" placeholder="2020"/></div>
        </div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Driver</label><input id="v-driver" class="bs-input" placeholder="Driver name"/></div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveVehicle()">Add Vehicle</button>
          <button class="btn btn-secondary" onclick="document.getElementById('veh-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let vehicles=[
  {id:1,plate:'T 445 DDD',type:'Truck',model:'Isuzu NKR',year:2021,driver:'Musa Juma',status:'On Route',km:45200,fuel:65,next_service:'2026-08-15'},
  {id:2,plate:'T 123 ABC',type:'Van',model:'Toyota HiAce',year:2019,driver:'Said Hassan',status:'Available',km:88400,fuel:40,next_service:'2026-09-01'},
  {id:3,plate:'T 789 XYZ',type:'Pickup',model:'Toyota Hilux',year:2022,driver:'Ally Omar',status:'Maintenance',km:32100,fuel:0,next_service:'2026-07-28'},
  {id:4,plate:'T 321 EFG',type:'Car',model:'Toyota Corolla',year:2020,driver:'Grace Kimani',status:'Available',km:55000,fuel:80,next_service:'2026-10-01'},
];
const VS={Available:'#16A34A','On Route':'#2563EB',Maintenance:'#EF4444',Idle:'#94A3B8'};
function render(){
  const available=vehicles.filter(v=>v.status==='Available').length;
  const onRoute=vehicles.filter(v=>v.status==='On Route').length;
  const maintenance=vehicles.filter(v=>v.status==='Maintenance').length;
  const totalKm=vehicles.reduce((s,v)=>s+(v.km||0),0);
  document.getElementById('flt-kpis').innerHTML=
    buildKpi('Total Vehicles',String(vehicles.length),'',null,'#2563EB')+
    buildKpi('On Route',String(onRoute),'',null,'#16A34A')+
    buildKpi('In Maintenance',String(maintenance),'',null,'#EF4444')+
    buildKpi('Total Mileage',fmt(totalKm)+' km','',null,'#7C3AED');
  document.getElementById('fleet-table').innerHTML=buildTable(
    ['Plate','Type','Model','Year','Driver','Mileage','Fuel %','Status','Next Service',''],
    vehicles,v=>`<tr>
      <td class="font-mono font-semibold text-[#111827]">${v.plate}</td>
      <td><span class="pill bg-slate-50 text-slate-600">${v.type}</span></td>
      <td class="font-medium">${v.model}</td>
      <td>${v.year}</td>
      <td class="text-slate-500">${v.driver}</td>
      <td>${fmt(v.km)} km</td>
      <td><div class="flex items-center gap-1"><div class="flex-1 h-1.5 bg-slate-100 rounded-full"><div class="h-full rounded-full bg-[#16A34A]" style="width:${v.fuel}%"></div></div><span class="text-[10px]">${v.fuel}%</span></div></td>
      <td>${buildPill(v.status,VS[v.status]||'#94A3B8')}</td>
      <td class="text-slate-500 text-[11px]">${v.next_service}</td>
      <td><select onchange="updateVehicle(${v.id},this.value)" class="text-[11px] border border-slate-200 rounded-lg px-2 py-1">
        ${['Available','On Route','Maintenance','Idle'].map(s=>`<option ${s===v.status?'selected':''} value="${s}">${s}</option>`).join('')}
      </select></td>
    </tr>`);
  lucide.createIcons();
}
render();
function openVehicleForm(){document.getElementById('veh-modal').classList.remove('hidden');}
async function saveVehicle(){
  const plate=document.getElementById('v-plate').value.trim();
  if(!plate){toast('Plate number required','error');return;}
  vehicles.unshift({id:uid(),plate,type:document.getElementById('v-type').value,
    model:document.getElementById('v-model').value,year:Number(document.getElementById('v-year').value)||2024,
    driver:document.getElementById('v-driver').value,status:'Available',km:0,fuel:100,next_service:'TBD'});
  try{await insertRow('fleet_vehicles',{plate_number:plate,status:'Available'});toast('Vehicle added!');}catch(e){toast('Added locally');}
  document.getElementById('veh-modal').classList.add('hidden');render();
}
function updateVehicle(id,status){vehicles=vehicles.map(v=>v.id==id?{...v,status}:v);updateRow('fleet_vehicles',id,{status}).catch(()=>{});render();toast('Updated');}
</script>