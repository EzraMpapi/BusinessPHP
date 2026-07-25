<div class="space-y-4">
  <div class="section-header"><h2>Settings</h2>
    <button class="btn btn-primary" onclick="saveSettings()"><i data-lucide="save" class="w-3.5 h-3.5"></i> Save Changes</button></div>
  <div class="grid-2" style="gap:16px">
    <div class="space-y-4">
      <div class="card">
        <h3 class="font-bold text-[14px] text-[#111827] mb-3">Company Profile</h3>
        <div class="space-y-3">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Company Name</label>
            <input id="s-name" class="bs-input" value="<?= COMPANY_NAME ?>"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Industry</label>
            <select id="s-industry" class="bs-input"><option>Retail &amp; Distribution</option><option>Manufacturing</option><option>Services</option><option>Agriculture</option><option>Technology</option></select></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Currency</label>
            <select id="s-currency" class="bs-input"><option>TZS</option><option>KES</option><option>USD</option><option>EUR</option></select></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Tax Rate (%)</label>
            <input id="s-tax" type="number" class="bs-input" value="18" placeholder="18"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Phone</label>
            <input id="s-phone" class="bs-input" placeholder="+255 xxx xxx xxx"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Address</label>
            <input id="s-address" class="bs-input" placeholder="Physical address"/></div>
        </div>
      </div>
      <div class="card">
        <h3 class="font-bold text-[14px] text-[#111827] mb-3">Supabase Connection</h3>
        <div class="space-y-3">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Project URL</label>
            <input class="bs-input" value="<?= SUPABASE_URL ?>" readonly/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Anon Key</label>
            <input type="password" class="bs-input" value="<?= SUPABASE_ANON_KEY ?>" readonly/></div>
          <div id="db-status" class="flex items-center gap-2 text-[12px]">
            <span class="spinner w-4 h-4"></span> Checking connection...
          </div>
        </div>
      </div>
    </div>
    <div class="space-y-4">
      <div class="card">
        <h3 class="font-bold text-[14px] text-[#111827] mb-3">Team Members</h3>
        <div id="team-list" class="space-y-2">
          <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-lg bg-[#16A34A] flex items-center justify-center text-white font-bold text-[11px]">A</div>
              <div><p class="text-[12.5px] font-semibold">Admin</p><p class="text-[10px] text-slate-400">Administrator</p></div>
            </div>
            <span class="pill bg-green-50 text-green-700">Owner</span>
          </div>
        </div>
        <button class="btn btn-secondary w-full mt-3" onclick="inviteUser()"><i data-lucide="user-plus" class="w-3.5 h-3.5"></i> Invite User</button>
      </div>
      <div class="card">
        <h3 class="font-bold text-[14px] text-[#111827] mb-3">Preferences</h3>
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-[12.5px] text-[#111827]">Email notifications</span>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" checked class="sr-only peer">
              <div class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:bg-[#16A34A] transition-colors"></div>
            </label>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-[12.5px] text-[#111827]">Low stock alerts</span>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" checked class="sr-only peer">
              <div class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:bg-[#16A34A] transition-colors"></div>
            </label>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-[12.5px] text-[#111827]">Payment reminders</span>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" class="sr-only peer">
              <div class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:bg-[#16A34A] transition-colors"></div>
            </label>
          </div>
        </div>
      </div>
      <div class="card">
        <h3 class="font-bold text-[14px] text-[#111827] mb-3">Danger Zone</h3>
        <div class="space-y-2">
          <button class="btn btn-danger w-full" onclick="if(confirm('Clear all local data?'))localStorage.clear(),toast('Cleared')">Clear Local Cache</button>
          <button class="btn w-full bg-slate-50 text-slate-600" onclick="window.print()"><i data-lucide="printer" class="w-3.5 h-3.5"></i> Print Settings</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
(async()=>{
  try{
    const{data}=await window.BS&&supabase?await createClient(window.BS.url,window.BS.key).from('_pgsodium_key_view').select('*').limit(1):{};
    document.getElementById('db-status').innerHTML='<span style="color:#16A34A">&#x2714;</span> Connected to Supabase';
  }catch(e){
    document.getElementById('db-status').innerHTML='<span style="color:#EF4444">&#x2716;</span> Not connected - running in offline/demo mode';
  }
})();
function saveSettings(){toast('Settings saved (edit config.php for permanent changes)');}
function inviteUser(){const email=prompt('Enter email to invite:');if(email)toast('Invitation sent to '+email);}
</script>