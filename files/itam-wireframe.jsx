import React, { useState } from 'react';
import { Menu, X, Home, Package, Users, FileText, Settings, LogOut, Search, Plus, Edit, Trash2, Eye, ChevronDown, Calendar, DollarSign, CheckCircle, XCircle, Download, Filter, Bell, User } from 'lucide-react';

const ITAMWireframe = () => {
  const [currentPage, setCurrentPage] = useState('login');
  const [userRole, setUserRole] = useState('admin');
  const [deviceView, setDeviceView] = useState('desktop');
  const [sidebarOpen, setSidebarOpen] = useState(true);

  // Device width mapping
  const deviceWidths = {
    mobile: 'max-w-sm',
    tablet: 'max-w-2xl',
    desktop: 'max-w-7xl'
  };

  // Sample data
  const assets = [
    { id: 'AST-001', name: 'Dell Laptop XPS 15', category: 'Computer', serial: 'DL123456', status: 'Available', price: '$1,200' },
    { id: 'AST-002', name: 'iPhone 15 Pro', category: 'Phone', serial: 'IP789012', status: 'In Use', price: '$999', assignedTo: 'John Doe' },
    { id: 'AST-003', name: 'HP Printer LaserJet', category: 'Printer', serial: 'HP345678', status: 'Available', price: '$450' }
  ];

  const users = [
    { id: 1, name: 'Admin User', email: 'admin@pline.com', role: 'Admin', status: 'Active' },
    { id: 2, name: 'John Doe', email: 'john@pline.com', role: 'User', status: 'Active' },
    { id: 3, name: 'Jane Smith', email: 'jane@pline.com', role: 'User', status: 'Active' }
  ];

  // Glassmorphism card component
  const GlassCard = ({ children, className = '' }) => (
    <div className={`bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 ${className}`}>
      {children}
    </div>
  );

  // Stat Card Component
  const StatCard = ({ title, value, icon: Icon, color, trend }) => (
    <GlassCard className="p-6 hover:shadow-2xl transition-all">
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm text-gray-600 mb-1">{title}</p>
          <h3 className="text-3xl font-bold text-gray-800">{value}</h3>
          {trend && <p className="text-xs text-green-600 mt-1">{trend}</p>}
        </div>
        <div className={`p-4 rounded-xl ${color}`}>
          <Icon className="w-8 h-8 text-white" />
        </div>
      </div>
    </GlassCard>
  );

  // Navigation Component
  const Navigation = ({ role }) => {
    const adminMenu = [
      { name: 'Dashboard', icon: Home, page: 'admin-dashboard' },
      { name: 'Assets', icon: Package, page: 'asset-list' },
      { name: 'Users', icon: Users, page: 'user-list' },
      { name: 'Reports', icon: FileText, page: 'reports' },
      { name: 'Profile', icon: Settings, page: 'profile' }
    ];

    const userMenu = [
      { name: 'Dashboard', icon: Home, page: 'user-dashboard' },
      { name: 'My Assets', icon: Package, page: 'my-assets' },
      { name: 'Profile', icon: Settings, page: 'profile' }
    ];

    const menuItems = role === 'admin' ? adminMenu : userMenu;

    return (
      <div className={`${sidebarOpen ? 'w-64' : 'w-20'} transition-all duration-300 bg-gradient-to-b from-blue-600 to-blue-800 text-white h-screen fixed left-0 top-0 z-40`}>
        <div className="p-6 flex items-center justify-between">
          {sidebarOpen && <h1 className="text-2xl font-bold">ITAM System</h1>}
          <button onClick={() => setSidebarOpen(!sidebarOpen)} className="p-2 hover:bg-white/10 rounded-lg">
            {sidebarOpen ? <X size={20} /> : <Menu size={20} />}
          </button>
        </div>
        
        <nav className="mt-8">
          {menuItems.map((item) => (
            <button
              key={item.page}
              onClick={() => setCurrentPage(item.page)}
              className={`w-full px-6 py-3 flex items-center gap-4 hover:bg-white/10 transition-colors ${
                currentPage === item.page ? 'bg-white/20 border-l-4 border-white' : ''
              }`}
            >
              <item.icon size={20} />
              {sidebarOpen && <span>{item.name}</span>}
            </button>
          ))}
          
          <button className="w-full px-6 py-3 flex items-center gap-4 hover:bg-white/10 transition-colors mt-auto absolute bottom-4">
            <LogOut size={20} />
            {sidebarOpen && <span>Logout</span>}
          </button>
        </nav>
      </div>
    );
  };

  // Header Component
  const Header = ({ title }) => (
    <div className="bg-white/80 backdrop-blur-lg border-b border-gray-200 px-8 py-4 flex items-center justify-between">
      <div>
        <h1 className="text-2xl font-bold text-gray-800">{title}</h1>
        <p className="text-sm text-gray-600">P-line Company - Vientiane, Laos</p>
      </div>
      <div className="flex items-center gap-4">
        <button className="p-2 hover:bg-gray-100 rounded-lg relative">
          <Bell size={20} />
          <span className="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>
        <div className="flex items-center gap-2">
          <div className="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
            {userRole === 'admin' ? 'A' : 'U'}
          </div>
          <div>
            <p className="text-sm font-semibold">{userRole === 'admin' ? 'Admin User' : 'John Doe'}</p>
            <p className="text-xs text-gray-500">{userRole}</p>
          </div>
        </div>
      </div>
    </div>
  );

  // Page Renderers
  const LoginPage = () => (
    <div className="min-h-screen bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 flex items-center justify-center p-4">
      <GlassCard className="w-full max-w-md p-8">
        <div className="text-center mb-8">
          <div className="w-20 h-20 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl mx-auto mb-4 flex items-center justify-center">
            <Package className="w-10 h-10 text-white" />
          </div>
          <h1 className="text-3xl font-bold text-gray-800">ITAM System</h1>
          <p className="text-gray-600 mt-2">IT Asset Management - P-line Company</p>
        </div>

        <form className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input
              type="email"
              className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              placeholder="admin@pline.com"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input
              type="password"
              className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              placeholder="••••••••"
            />
          </div>

          <div className="flex items-center justify-between">
            <label className="flex items-center">
              <input type="checkbox" className="rounded text-blue-600" />
              <span className="ml-2 text-sm text-gray-600">Remember me</span>
            </label>
            <a href="#" className="text-sm text-blue-600 hover:text-blue-700">Forgot password?</a>
          </div>

          <button
            type="button"
            onClick={() => {
              setCurrentPage('admin-dashboard');
              setUserRole('admin');
            }}
            className="w-full py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all"
          >
            Sign In
          </button>
        </form>

        <div className="mt-6 flex gap-2">
          <button
            onClick={() => {
              setCurrentPage('admin-dashboard');
              setUserRole('admin');
            }}
            className="flex-1 py-2 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200"
          >
            Demo as Admin
          </button>
          <button
            onClick={() => {
              setCurrentPage('user-dashboard');
              setUserRole('user');
            }}
            className="flex-1 py-2 text-xs bg-green-100 text-green-700 rounded-lg hover:bg-green-200"
          >
            Demo as User
          </button>
        </div>
      </GlassCard>
    </div>
  );

  const AdminDashboard = () => (
    <div className="p-8 space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard title="Total Assets" value="156" icon={Package} color="bg-gradient-to-br from-blue-500 to-blue-600" trend="+12 this month" />
        <StatCard title="Available" value="89" icon={CheckCircle} color="bg-gradient-to-br from-green-500 to-green-600" trend="57% available" />
        <StatCard title="In Use" value="67" icon={XCircle} color="bg-gradient-to-br from-orange-500 to-orange-600" trend="43% in use" />
        <StatCard title="Total Value" value="$245K" icon={DollarSign} color="bg-gradient-to-br from-purple-500 to-purple-600" trend="+5.2% value" />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <GlassCard className="p-6">
          <h2 className="text-xl font-bold text-gray-800 mb-4">Recent Activities</h2>
          <div className="space-y-3">
            {[
              { action: 'Check Out', asset: 'Dell Laptop XPS 15', user: 'John Doe', time: '2 hours ago', type: 'out' },
              { action: 'Check In', asset: 'iPhone 15 Pro', user: 'Jane Smith', time: '5 hours ago', type: 'in' },
              { action: 'New Asset', asset: 'HP Printer LaserJet', user: 'Admin', time: '1 day ago', type: 'new' }
            ].map((activity, idx) => (
              <div key={idx} className="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                <div className="flex items-center gap-3">
                  <div className={`w-10 h-10 rounded-full flex items-center justify-center ${
                    activity.type === 'out' ? 'bg-orange-100' : activity.type === 'in' ? 'bg-green-100' : 'bg-blue-100'
                  }`}>
                    <Package size={18} className={
                      activity.type === 'out' ? 'text-orange-600' : activity.type === 'in' ? 'text-green-600' : 'text-blue-600'
                    } />
                  </div>
                  <div>
                    <p className="font-medium text-gray-800">{activity.action}: {activity.asset}</p>
                    <p className="text-sm text-gray-600">{activity.user}</p>
                  </div>
                </div>
                <p className="text-xs text-gray-500">{activity.time}</p>
              </div>
            ))}
          </div>
        </GlassCard>

        <GlassCard className="p-6">
          <h2 className="text-xl font-bold text-gray-800 mb-4">Assets by Category</h2>
          <div className="space-y-4">
            {[
              { name: 'Computers', count: 45, percentage: 65, color: 'bg-blue-500' },
              { name: 'Phones', count: 32, percentage: 85, color: 'bg-green-500' },
              { name: 'Printers', count: 18, percentage: 45, color: 'bg-purple-500' },
              { name: 'Accessories', count: 61, percentage: 92, color: 'bg-orange-500' }
            ].map((category, idx) => (
              <div key={idx}>
                <div className="flex items-center justify-between mb-2">
                  <span className="text-sm font-medium text-gray-700">{category.name}</span>
                  <span className="text-sm text-gray-600">{category.count} items</span>
                </div>
                <div className="w-full bg-gray-200 rounded-full h-2">
                  <div className={`${category.color} h-2 rounded-full transition-all`} style={{ width: `${category.percentage}%` }}></div>
                </div>
              </div>
            ))}
          </div>
        </GlassCard>
      </div>

      <GlassCard className="p-6">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-xl font-bold text-gray-800">Quick Actions</h2>
        </div>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {[
            { label: 'Add Asset', icon: Plus, color: 'from-blue-500 to-blue-600', page: 'add-asset' },
            { label: 'Add User', icon: Users, color: 'from-green-500 to-green-600', page: 'add-user' },
            { label: 'Generate Report', icon: FileText, color: 'from-purple-500 to-purple-600', page: 'reports' },
            { label: 'Check Out', icon: Package, color: 'from-orange-500 to-orange-600', page: 'check-out' }
          ].map((action, idx) => (
            <button
              key={idx}
              onClick={() => setCurrentPage(action.page)}
              className={`p-6 bg-gradient-to-br ${action.color} text-white rounded-xl hover:shadow-lg transition-all flex flex-col items-center gap-2`}
            >
              <action.icon size={24} />
              <span className="font-medium">{action.label}</span>
            </button>
          ))}
        </div>
      </GlassCard>
    </div>
  );

  const AssetListPage = () => (
    <div className="p-8 space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-gray-800">Asset Management</h1>
          <p className="text-gray-600">Manage and track all IT assets</p>
        </div>
        <button
          onClick={() => setCurrentPage('add-asset')}
          className="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all flex items-center gap-2"
        >
          <Plus size={20} />
          Add New Asset
        </button>
      </div>

      <GlassCard className="p-6">
        <div className="flex flex-col md:flex-row gap-4 mb-6">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
            <input
              type="text"
              placeholder="Search by name, serial number..."
              className="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <select className="px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
            <option>All Categories</option>
            <option>Computer</option>
            <option>Phone</option>
            <option>Printer</option>
          </select>
          <select className="px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
            <option>All Status</option>
            <option>Available</option>
            <option>In Use</option>
          </select>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b border-gray-200">
                <th className="text-left py-4 px-4 text-sm font-semibold text-gray-700">Asset Code</th>
                <th className="text-left py-4 px-4 text-sm font-semibold text-gray-700">Asset Name</th>
                <th className="text-left py-4 px-4 text-sm font-semibold text-gray-700">Category</th>
                <th className="text-left py-4 px-4 text-sm font-semibold text-gray-700">Serial Number</th>
                <th className="text-left py-4 px-4 text-sm font-semibold text-gray-700">Status</th>
                <th className="text-left py-4 px-4 text-sm font-semibold text-gray-700">Price</th>
                <th className="text-left py-4 px-4 text-sm font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody>
              {assets.map((asset) => (
                <tr key={asset.id} className="border-b border-gray-100 hover:bg-gray-50">
                  <td className="py-4 px-4">
                    <span className="font-mono text-sm font-semibold text-blue-600">{asset.id}</span>
                  </td>
                  <td className="py-4 px-4">
                    <p className="font-medium text-gray-800">{asset.name}</p>
                    {asset.assignedTo && <p className="text-xs text-gray-500">Assigned to: {asset.assignedTo}</p>}
                  </td>
                  <td className="py-4 px-4">
                    <span className="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                      {asset.category}
                    </span>
                  </td>
                  <td className="py-4 px-4">
                    <span className="font-mono text-sm text-gray-600">{asset.serial}</span>
                  </td>
                  <td className="py-4 px-4">
                    <span className={`px-3 py-1 rounded-full text-xs font-medium ${
                      asset.status === 'Available' 
                        ? 'bg-green-100 text-green-700' 
                        : 'bg-orange-100 text-orange-700'
                    }`}>
                      {asset.status}
                    </span>
                  </td>
                  <td className="py-4 px-4">
                    <span className="font-semibold text-gray-800">{asset.price}</span>
                  </td>
                  <td className="py-4 px-4">
                    <div className="flex items-center gap-2">
                      <button className="p-2 hover:bg-blue-100 rounded-lg text-blue-600 transition-colors">
                        <Eye size={16} />
                      </button>
                      <button className="p-2 hover:bg-green-100 rounded-lg text-green-600 transition-colors">
                        <Edit size={16} />
                      </button>
                      <button className="p-2 hover:bg-red-100 rounded-lg text-red-600 transition-colors">
                        <Trash2 size={16} />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        <div className="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
          <p className="text-sm text-gray-600">Showing 1 to 3 of 156 assets</p>
          <div className="flex gap-2">
            <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Previous</button>
            <button className="px-4 py-2 bg-blue-600 text-white rounded-lg">1</button>
            <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">2</button>
            <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">3</button>
            <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Next</button>
          </div>
        </div>
      </GlassCard>
    </div>
  );

  const AddAssetPage = () => (
    <div className="p-8 space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-gray-800">Add New Asset</h1>
          <p className="text-gray-600">Fill in the asset details below</p>
        </div>
        <button
          onClick={() => setCurrentPage('asset-list')}
          className="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all"
        >
          Back to List
        </button>
      </div>

      <GlassCard className="p-8">
        <form className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Asset Code *</label>
              <input
                type="text"
                className="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-100 focus:ring-2 focus:ring-blue-500"
                placeholder="Auto-generated: AST-XXXX"
                disabled
              />
              <p className="text-xs text-gray-500 mt-1">This will be auto-generated</p>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Asset Name *</label>
              <input
                type="text"
                className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500"
                placeholder="e.g., Dell Laptop XPS 15"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Category *</label>
              <select className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
                <option>Select Category</option>
                <option>Computer</option>
                <option>Laptop</option>
                <option>Phone</option>
                <option>Tablet</option>
                <option>Printer</option>
                <option>Monitor</option>
                <option>Accessories</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
              <input
                type="text"
                className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500"
                placeholder="e.g., DL123456789"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Brand</label>
              <input
                type="text"
                className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500"
                placeholder="e.g., Dell, HP, Apple"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Model</label>
              <input
                type="text"
                className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500"
                placeholder="e.g., XPS 15"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Purchase Date</label>
              <input
                type="date"
                className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Purchase Price ($)</label>
              <input
                type="number"
                className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500"
                placeholder="e.g., 1200"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Status *</label>
              <select className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
                <option>Available</option>
                <option>In Use</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Asset Photo</label>
              <div className="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-500 transition-colors cursor-pointer">
                <Package className="mx-auto text-gray-400 mb-2" size={32} />
                <p className="text-sm text-gray-600">Click to upload or drag and drop</p>
                <p className="text-xs text-gray-500">PNG, JPG up to 5MB</p>
              </div>
            </div>
          </div>

          <div className="flex gap-4 pt-6 border-t border-gray-200">
            <button
              type="submit"
              className="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all"
            >
              Save Asset
            </button>
            <button
              type="button"
              onClick={() => setCurrentPage('asset-list')}
              className="px-8 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all"
            >
              Cancel
            </button>
          </div>
        </form>
      </GlassCard>
    </div>
  );

  const UserDashboard = () => (
    <div className="p-8 space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <StatCard title="My Assets" value="3" icon={Package} color="bg-gradient-to-br from-blue-500 to-blue-600" />
        <StatCard title="Total Value" value="$3,649" icon={DollarSign} color="bg-gradient-to-br from-green-500 to-green-600" />
        <StatCard title="Last Check Out" value="2 days ago" icon={Calendar} color="bg-gradient-to-br from-purple-500 to-purple-600" />
      </div>

      <GlassCard className="p-6">
        <h2 className="text-xl font-bold text-gray-800 mb-4">My Assigned Assets</h2>
        <div className="space-y-4">
          {[
            { name: 'Dell Laptop XPS 15', serial: 'DL123456', assignedDate: '2024-01-15', category: 'Computer', value: '$1,200' },
            { name: 'iPhone 15 Pro', serial: 'IP789012', assignedDate: '2024-02-01', category: 'Phone', value: '$999' },
            { name: 'Apple Magic Mouse', serial: 'AM345678', assignedDate: '2024-01-15', category: 'Accessories', value: '$79' }
          ].map((asset, idx) => (
            <div key={idx} className="p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl hover:shadow-md transition-all">
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-4">
                  <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center">
                    <Package className="text-white" size={32} />
                  </div>
                  <div>
                    <h3 className="font-semibold text-gray-800">{asset.name}</h3>
                    <p className="text-sm text-gray-600">Serial: {asset.serial}</p>
                    <div className="flex items-center gap-4 mt-1">
                      <span className="text-xs text-gray-500">Category: {asset.category}</span>
                      <span className="text-xs text-gray-500">Assigned: {asset.assignedDate}</span>
                    </div>
                  </div>
                </div>
                <div className="text-right">
                  <p className="text-lg font-bold text-gray-800">{asset.value}</p>
                  <button className="mt-2 px-4 py-1 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                    View Details
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </GlassCard>

      <GlassCard className="p-6">
        <h2 className="text-xl font-bold text-gray-800 mb-4">My Activity History</h2>
        <div className="space-y-3">
          {[
            { action: 'Check Out', asset: 'iPhone 15 Pro', date: '2024-02-01 10:30 AM', type: 'out' },
            { action: 'Check Out', asset: 'Dell Laptop XPS 15', date: '2024-01-15 09:00 AM', type: 'out' },
            { action: 'Check In', asset: 'HP Printer', date: '2024-01-10 03:45 PM', type: 'in' }
          ].map((activity, idx) => (
            <div key={idx} className="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
              <div className="flex items-center gap-3">
                <div className={`w-10 h-10 rounded-full flex items-center justify-center ${
                  activity.type === 'out' ? 'bg-orange-100' : 'bg-green-100'
                }`}>
                  <Package size={18} className={activity.type === 'out' ? 'text-orange-600' : 'text-green-600'} />
                </div>
                <div>
                  <p className="font-medium text-gray-800">{activity.action}: {activity.asset}</p>
                  <p className="text-sm text-gray-600">{activity.date}</p>
                </div>
              </div>
            </div>
          ))}
        </div>
      </GlassCard>
    </div>
  );

  const ReportsPage = () => (
    <div className="p-8 space-y-6">
      <div>
        <h1 className="text-3xl font-bold text-gray-800">Reports & Analytics</h1>
        <p className="text-gray-600">Generate and export detailed reports</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {[
          { title: 'All Assets Report', desc: 'Complete list grouped by category and status', icon: Package, color: 'from-blue-500 to-blue-600' },
          { title: 'User Assets Report', desc: 'Assets assigned to specific users', icon: Users, color: 'from-green-500 to-green-600' },
          { title: 'Asset Value Report', desc: 'Total value by category and depreciation', icon: DollarSign, color: 'from-purple-500 to-purple-600' },
          { title: 'Activity Log Report', desc: 'Check-in/out history with date filters', icon: FileText, color: 'from-orange-500 to-orange-600' },
          { title: 'Category Analysis', desc: 'Assets distribution by categories', icon: Filter, color: 'from-pink-500 to-pink-600' },
          { title: 'Custom Report', desc: 'Build your own custom report', icon: Settings, color: 'from-indigo-500 to-indigo-600' }
        ].map((report, idx) => (
          <GlassCard key={idx} className="p-6 hover:shadow-2xl transition-all cursor-pointer group">
            <div className={`w-12 h-12 bg-gradient-to-br ${report.color} rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform`}>
              <report.icon className="text-white" size={24} />
            </div>
            <h3 className="text-lg font-bold text-gray-800 mb-2">{report.title}</h3>
            <p className="text-sm text-gray-600 mb-4">{report.desc}</p>
            <div className="flex gap-2">
              <button className="flex-1 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg text-sm hover:shadow-md transition-all flex items-center justify-center gap-2">
                <Download size={16} />
                PDF
              </button>
              <button className="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:shadow-md transition-all flex items-center justify-center gap-2">
                <Download size={16} />
                Excel
              </button>
            </div>
          </GlassCard>
        ))}
      </div>

      <GlassCard className="p-6">
        <h2 className="text-xl font-bold text-gray-800 mb-4">Generate Custom Report</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
            <select className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
              <option>Select Report Type</option>
              <option>Assets Report</option>
              <option>Users Report</option>
              <option>Activity Report</option>
              <option>Value Report</option>
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
            <select className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
              <option>Last 7 days</option>
              <option>Last 30 days</option>
              <option>Last 3 months</option>
              <option>Last year</option>
              <option>Custom range</option>
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">Filter by Category</label>
            <select className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
              <option>All Categories</option>
              <option>Computer</option>
              <option>Phone</option>
              <option>Printer</option>
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
            <select className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500">
              <option>All Status</option>
              <option>Available</option>
              <option>In Use</option>
            </select>
          </div>
        </div>

        <div className="flex gap-4 mt-6">
          <button className="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
            Generate Report
          </button>
          <button className="px-8 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all">
            Reset Filters
          </button>
        </div>
      </GlassCard>
    </div>
  );

  const ProfilePage = () => (
    <div className="p-8 space-y-6">
      <div>
        <h1 className="text-3xl font-bold text-gray-800">My Profile</h1>
        <p className="text-gray-600">Manage your account settings</p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <GlassCard className="p-6">
          <div className="text-center">
            <div className="w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full mx-auto mb-4 flex items-center justify-center text-white text-4xl font-bold">
              {userRole === 'admin' ? 'A' : 'J'}
            </div>
            <h3 className="text-xl font-bold text-gray-800">{userRole === 'admin' ? 'Admin User' : 'John Doe'}</h3>
            <p className="text-gray-600 mb-1">{userRole === 'admin' ? 'admin@pline.com' : 'john@pline.com'}</p>
            <span className={`inline-block px-4 py-1 rounded-full text-sm font-medium ${
              userRole === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'
            }`}>
              {userRole === 'admin' ? 'Administrator' : 'User'}
            </span>
            <div className="mt-6 space-y-2">
              <div className="flex items-center justify-between text-sm">
                <span className="text-gray-600">Account Status</span>
                <span className="text-green-600 font-semibold">Active</span>
              </div>
              <div className="flex items-center justify-between text-sm">
                <span className="text-gray-600">Member Since</span>
                <span className="text-gray-800">Jan 2024</span>
              </div>
              {userRole === 'user' && (
                <div className="flex items-center justify-between text-sm">
                  <span className="text-gray-600">Assets Assigned</span>
                  <span className="text-gray-800 font-semibold">3</span>
                </div>
              )}
            </div>
          </div>
        </GlassCard>

        <div className="lg:col-span-2 space-y-6">
          <GlassCard className="p-6">
            <h2 className="text-xl font-bold text-gray-800 mb-4">Account Information</h2>
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input
                  type="text"
                  className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500"
                  defaultValue={userRole === 'admin' ? 'Admin User' : 'John Doe'}
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input
                  type="email"
                  className="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-100"
                  defaultValue={userRole === 'admin' ? 'admin@pline.com' : 'john@pline.com'}
                  disabled
                />
                <p className="text-xs text-gray-500 mt-1">Email cannot be changed</p>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <input
                  type="text"
                  className="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-100"
                  defaultValue={userRole === 'admin' ? 'Administrator' : 'User'}
                  disabled
                />
              </div>

              <div className="flex gap-4 pt-4">
                <button className="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                  Update Profile
                </button>
                <button className="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all">
                  Cancel
                </button>
              </div>
            </div>
          </GlassCard>

          <GlassCard className="p-6">
            <h2 className="text-xl font-bold text-gray-800 mb-4">Change Password</h2>
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                <input
                  type="password"
                  className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500"
                  placeholder="••••••••"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input
                  type="password"
                  className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500"
                  placeholder="••••••••"
                />
                <p className="text-xs text-gray-500 mt-1">Minimum 8 characters with uppercase, lowercase, and number</p>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                <input
                  type="password"
                  className="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500"
                  placeholder="••••••••"
                />
              </div>

              <div className="flex gap-4 pt-4">
                <button className="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                  Change Password
                </button>
                <button className="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all">
                  Cancel
                </button>
              </div>
            </div>
          </GlassCard>
        </div>
      </div>
    </div>
  );

  // Main render with device preview
  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-100 to-gray-200 p-8">
      {/* Control Panel */}
      <div className="max-w-7xl mx-auto mb-6">
        <GlassCard className="p-4">
          <div className="flex flex-wrap items-center gap-4">
            <div>
              <h2 className="text-sm font-semibold text-gray-700 mb-2">View Controls</h2>
              <div className="flex gap-2">
                <button
                  onClick={() => setDeviceView('desktop')}
                  className={`px-4 py-2 rounded-lg font-medium transition-all ${
                    deviceView === 'desktop' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                  }`}
                >
                  Desktop
                </button>
                <button
                  onClick={() => setDeviceView('tablet')}
                  className={`px-4 py-2 rounded-lg font-medium transition-all ${
                    deviceView === 'tablet' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                  }`}
                >
                  Tablet
                </button>
                <button
                  onClick={() => setDeviceView('mobile')}
                  className={`px-4 py-2 rounded-lg font-medium transition-all ${
                    deviceView === 'mobile' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                  }`}
                >
                  Mobile
                </button>
              </div>
            </div>

            <div className="border-l border-gray-300 pl-4">
              <h2 className="text-sm font-semibold text-gray-700 mb-2">Quick Pages</h2>
              <div className="flex flex-wrap gap-2">
                <button onClick={() => setCurrentPage('login')} className="px-3 py-1 bg-purple-100 text-purple-700 rounded text-xs hover:bg-purple-200">Login</button>
                <button onClick={() => { setCurrentPage('admin-dashboard'); setUserRole('admin'); }} className="px-3 py-1 bg-blue-100 text-blue-700 rounded text-xs hover:bg-blue-200">Admin</button>
                <button onClick={() => { setCurrentPage('user-dashboard'); setUserRole('user'); }} className="px-3 py-1 bg-green-100 text-green-700 rounded text-xs hover:bg-green-200">User</button>
                <button onClick={() => setCurrentPage('asset-list')} className="px-3 py-1 bg-orange-100 text-orange-700 rounded text-xs hover:bg-orange-200">Assets</button>
                <button onClick={() => setCurrentPage('reports')} className="px-3 py-1 bg-pink-100 text-pink-700 rounded text-xs hover:bg-pink-200">Reports</button>
              </div>
            </div>

            <div className="ml-auto">
              <p className="text-xs text-gray-600">ITAM System - UI/UX Wireframe</p>
              <p className="text-xs text-gray-500">P-line Company</p>
            </div>
          </div>
        </GlassCard>
      </div>

      {/* Device Frame */}
      <div className="flex justify-center">
        <div className={`${deviceWidths[deviceView]} mx-auto transition-all duration-300`}>
          <div className="bg-white rounded-3xl shadow-2xl overflow-hidden">
            {currentPage === 'login' ? (
              <LoginPage />
            ) : (
              <div className="flex">
                {deviceView !== 'mobile' && <Navigation role={userRole} />}
                <div className={`flex-1 ${sidebarOpen && deviceView !== 'mobile' ? 'ml-64' : deviceView !== 'mobile' ? 'ml-20' : ''}`}>
                  <Header title={
                    currentPage === 'admin-dashboard' ? 'Admin Dashboard' :
                    currentPage === 'user-dashboard' ? 'My Dashboard' :
                    currentPage === 'asset-list' ? 'Asset Management' :
                    currentPage === 'add-asset' ? 'Add New Asset' :
                    currentPage === 'reports' ? 'Reports' :
                    currentPage === 'profile' ? 'Profile' :
                    'Dashboard'
                  } />
                  <div className="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
                    {currentPage === 'admin-dashboard' && <AdminDashboard />}
                    {currentPage === 'user-dashboard' && <UserDashboard />}
                    {currentPage === 'asset-list' && <AssetListPage />}
                    {currentPage === 'add-asset' && <AddAssetPage />}
                    {currentPage === 'reports' && <ReportsPage />}
                    {currentPage === 'profile' && <ProfilePage />}
                    {currentPage === 'my-assets' && <UserDashboard />}
                    {currentPage === 'user-list' && <div className="p-8"><h1 className="text-3xl font-bold">User Management (Coming Soon)</h1></div>}
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Mobile Navigation */}
      {currentPage !== 'login' && deviceView === 'mobile' && (
        <div className="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 flex justify-around z-50">
          <button onClick={() => setCurrentPage(userRole === 'admin' ? 'admin-dashboard' : 'user-dashboard')} className="flex flex-col items-center">
            <Home size={24} className={currentPage.includes('dashboard') ? 'text-blue-600' : 'text-gray-400'} />
            <span className="text-xs mt-1">Home</span>
          </button>
          <button onClick={() => setCurrentPage(userRole === 'admin' ? 'asset-list' : 'my-assets')} className="flex flex-col items-center">
            <Package size={24} className={currentPage.includes('asset') ? 'text-blue-600' : 'text-gray-400'} />
            <span className="text-xs mt-1">Assets</span>
          </button>
          {userRole === 'admin' && (
            <button onClick={() => setCurrentPage('reports')} className="flex flex-col items-center">
              <FileText size={24} className={currentPage === 'reports' ? 'text-blue-600' : 'text-gray-400'} />
              <span className="text-xs mt-1">Reports</span>
            </button>
          )}
          <button onClick={() => setCurrentPage('profile')} className="flex flex-col items-center">
            <User size={24} className={currentPage === 'profile' ? 'text-blue-600' : 'text-gray-400'} />
            <span className="text-xs mt-1">Profile</span>
          </button>
        </div>
      )}
    </div>
  );
};

export default ITAMWireframe;
