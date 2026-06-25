import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { DollarSign, Users, Home, TrendingUp, TrendingDown, Wallet } from 'lucide-react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, LineChart, Line, Legend } from 'recharts';
import api from '../lib/api';

export default function DashboardPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['dashboard'],
    queryFn: async () => {
      const res = await api.get('/dashboard');
      return res.data.data;
    },
  });

  if (isLoading) {
    return <div className="flex items-center justify-center h-64">Loading...</div>;
  }

  const chartData = data?.grafik_pemasukan_pengeluaran;
  const paymentChart = data?.grafik_pembayaran_iuran;

  const stats = [
    { label: 'Total Rumah', value: data?.total_rumah || 0, icon: Home, color: 'bg-blue-500' },
    { label: 'Rumah Dihuni', value: data?.rumah_dihuni || 0, icon: Home, color: 'bg-green-500' },
    { label: 'Rumah Kosong', value: data?.rumah_kosong || 0, icon: Home, color: 'bg-gray-500' },
    { label: 'Total Penghuni', value: data?.total_penghuni || 0, icon: Users, color: 'bg-purple-500' },
    { label: 'Penghuni Tetap', value: data?.penghuni_tetap || 0, icon: Users, color: 'bg-indigo-500' },
    { label: 'Penghuni Kontrak', value: data?.penghuni_kontrak || 0, icon: Users, color: 'bg-orange-500' },
  ];

  const financialStats = [
    { label: 'Pemasukan Bulan Ini', value: data?.total_pemasukan_bulan_ini || 0, icon: TrendingUp, color: 'text-green-600' },
    { label: 'Pengeluaran Bulan Ini', value: data?.total_pengeluaran_bulan_ini || 0, icon: TrendingDown, color: 'text-red-600' },
    { label: 'Saldo', value: data?.saldo || 0, icon: Wallet, color: 'text-primary-600' },
  ];

  const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
  };

  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">Ringkasan administrasi RT</p>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {stats.map((stat) => (
          <div key={stat.label} className="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-500 dark:text-gray-400">{stat.label}</p>
                <p className="text-2xl font-bold text-gray-900 dark:text-white mt-1">{stat.value}</p>
              </div>
              <div className={`w-12 h-12 ${stat.color} rounded-xl flex items-center justify-center`}>
                <stat.icon className="w-6 h-6 text-white" />
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Financial Stats */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {financialStats.map((stat) => (
          <div key={stat.label} className="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-500 dark:text-gray-400">{stat.label}</p>
                <p className={`text-2xl font-bold mt-1 ${stat.color}`}>
                  {formatCurrency(stat.value)}
                </p>
              </div>
              <div className="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center">
                <DollarSign className={`w-6 h-6 ${stat.color}`} />
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800">
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Pemasukan & Pengeluaran {chartData?.tahun || new Date().getFullYear()}
          </h3>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={months.map((m, i) => ({
              nama: m,
              pemasukan: chartData?.pemasukan?.[i] || 0,
              pengeluaran: chartData?.pengeluaran?.[i] || 0,
            }))}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="nama" />
              <YAxis />
              <Tooltip formatter={(value) => formatCurrency(value)} />
              <Legend />
              <Bar dataKey="pemasukan" fill="#3b82f6" name="Pemasukan" />
              <Bar dataKey="pengeluaran" fill="#ef4444" name="Pengeluaran" />
            </BarChart>
          </ResponsiveContainer>
        </div>

        <div className="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800">
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Pembayaran Iuran {new Date().getFullYear()}
          </h3>
          <ResponsiveContainer width="100%" height={300}>
            <LineChart data={paymentChart?.map((item) => ({
              nama: months[item.bulan - 1],
              tagihan: item.total_tagihan,
              lunas: item.total_lunas,
            }))}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="nama" />
              <YAxis />
              <Tooltip formatter={(value) => formatCurrency(value)} />
              <Legend />
              <Line type="monotone" dataKey="tagihan" stroke="#94a3b8" name="Total Tagihan" strokeWidth={2} />
              <Line type="monotone" dataKey="lunas" stroke="#22c55e" name="Total Lunas" strokeWidth={2} />
            </LineChart>
          </ResponsiveContainer>
        </div>
      </div>
    </div>
  );
}