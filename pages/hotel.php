<div class="space-y-4">
  <div class="section-header"><h2>Hotel Management</h2>
    <button class="btn btn-primary" onclick="openBookingForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> New Booking</button></div>
  <div id="htl-kpis" class="grid-4" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div></div>
  <div class="grid-2" style="gap:16px">
    <div><h3 class="text-[14px] font-bold text-[#111827] mb-3">Room Status</h3><div id="rooms-grid" class="grid-3" style="gap:8px"></div></div>
    <div><h3 class="text-[14px] font-bold text-[#111827] mb-3">Today's Bookings</h3><div id="booking-table"></div></div>
  </div>
  <div id="book-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md"><div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px]">New Booking</h3>
      <button onclick="document.getElementById('book-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Guest Name *</label><input id="g-name" class="bs-input" placeholder="Full name"/></div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Room Type</label>
            <select id="g-room" class="bs-input"><option>Standard</option><option>Deluxe</option><option>Suite</option></select></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Nights</label><input id="g-nights" type="number" class="bs-input" value="1" min="1"/></div>
        </div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Check-in</label><input id="g-in" type="date" class="bs-input"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Check-out</label><input id="g-out" type="date" class="bs-input"/></div>
        </div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveBooking()">Book Room</button>
          <button class="btn btn-secondary" onclick="document.getElementById('book-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
const RATES={Standard:80000,Deluxe:150000,Suite:300000};
let rooms=Array.from({length:20},(_,i)=>({number:101+i,type:i<8?'Standard':i<15?'Deluxe':'Suite',status:['Available','Occupied','Available','Cleaning'][i%4]}));
let bookings=[
  {id:'BK-201',guest:'Mr. James Omondi',room_type:'Suite',room:118,nights:3,amount:900000,check_in:today(),check_out:today(),status:'Checked In'},
  {id:'BK-200',guest:'Ms. Grace Kimani',room_type:'Deluxe',room:109,nights:1,amount:150000,check_in:today(),check_out:today(),status:'Checked In'},
  {id:'BK-199',guest:'Mr. Hassan Ally',room_type:'Standard',room:103,nights:2,amount:160000,check_in:today(),check_out:today(),status:'Due Check-out'},
];
const RC={Available:'#16A34A',Occupied:'#EF4444',Cleaning:'#F59E0B',Reserved:'#2563EB'};
const BS={'Checked In':'#16A34A','Due Check-out':'#EF4444',Reserved:'#2563EB','Checked Out':'#94A3B8'};
function render(){
  const occ=rooms.filter(r=>r.status==='Occupied').length;
  const avail=rooms.filter(r=>r.status==='Available').length;
  const rev=bookings.reduce((s,b)=>s+(b.amount||0),0);
  document.getElementById('htl-kpis').innerHTML=
    buildKpi('Total Rooms',String(rooms.length),'',null,'#2563EB')+
    buildKpi('Occupied',String(occ),'',null,'#EF4444')+
    buildKpi('Available',String(avail),'',null,'#16A34A')+
    buildKpi('Today Revenue',money(rev),'',null,'#7C3AED');
  document.getElementById('rooms-grid').innerHTML=rooms.map(r=>`
    <div class="card p-2 text-center cursor-pointer" style="border-color:${RC[r.status]}40">
      <p class="text-[12px] font-bold text-[#111827]">${r.number}</p>
      <p class="text-[10px]" style="color:${RC[r.status]}">${r.status}</p>
      <p class="text-[9px] text-slate-400">${r.type}</p>
    </div>`).join('');
  document.getElementById('booking-table').innerHTML=buildTable(
    ['Guest','Room','Type','Nights','Amount','Status'],
    bookings,b=>`<tr>
      <td class="font-semibold text-[#111827]">${b.guest}</td>
      <td class="font-mono">${b.room}</td>
      <td>${b.room_type}</td>
      <td>${b.nights}</td>
      <td class="font-bold">${money(b.amount)}</td>
      <td>${buildPill(b.status,BS[b.status]||'#94A3B8')}</td>
    </tr>`);
  lucide.createIcons();
}
render();
function openBookingForm(){const d=new Date();document.getElementById('g-in').value=today();document.getElementById('book-modal').classList.remove('hidden');}
async function saveBooking(){
  const guest=document.getElementById('g-name').value.trim();
  if(!guest){toast('Guest name required','error');return;}
  const type=document.getElementById('g-room').value;
  const nights=Number(document.getElementById('g-nights').value)||1;
  const amount=RATES[type]*nights;
  const avail=rooms.find(r=>r.type===type&&r.status==='Available');
  if(!avail){toast('No '+type+' rooms available','error');return;}
  rooms=rooms.map(r=>r.number===avail.number?{...r,status:'Occupied'}:r);
  bookings.unshift({id:'BK-'+uid(),guest,room_type:type,room:avail.number,nights,amount,
    check_in:document.getElementById('g-in').value,check_out:document.getElementById('g-out').value,status:'Checked In'});
  try{await insertRow('hotel_bookings',{guest_name:guest,room_type:type,amount,status:'Checked In'});toast('Booking confirmed!');}catch(e){toast('Added locally');}
  document.getElementById('book-modal').classList.add('hidden');render();
}
</script>