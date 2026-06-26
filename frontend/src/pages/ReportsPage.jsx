import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { FileText, TrendingUp, TrendingDown, Wallet } from 'lucide-react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, LineChart, Line, Legend } from 'recharts';
import api from '../lib/api';

export default function ReportsPage() {
  const [bulan, setBulan] = React.useState(new Date().getMonth() + 1);
  const [tahun, setTahun] = React.useState(new Date().getFullYear());
  const [activeTab, setActiveTab] = React.useState('summary');

  const { data: summary } = useQuery({
    queryKey: ['reports-summary', bulan, tahun],
    queryFn: async () => {
      const res = await api.get('/reports/summary', { params: { bulan, tahun } });
      return res.data.data;
    },
    enabled: activeTab === 'summary',
  });

  const { data: chart } = useQuery({
    queryKey: ['reports-chart', tahun],
    queryFn: async () => {
      const res = await api.get('/reports/chart', { params: { tahun } });
      return res.data.data;
    },
    enabled: activeTab === 'chart',
  });

  const { data: detail } = useQuery({
    queryKey: ['reports-detail', bulan, tahun],
    queryFn: async () => {
      const res = await api.get('/reports/detail', { params: { bulan, tahun } });
      return res.data.data;
    },
    enabled: activeTab === 'detail',
  });

  const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
  };

  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
  const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

  return (
    <div className="space-y-4">
      <div>
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Laporan</h1>
        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">Laporan keuangan dan statistik RT</p>
      </div>

      <div className="flex flex-col md:flex-row gap-3">
        <select
          value={bulan}
          onChange={(e) => setBulan(parseInt(e.target.value))}
          className="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
        >
          {monthNames.map((m, i) => (
            <option key={i} value={i + 1}>{m}</option>
          ))}
        </select>
        <select
          value={tahun}
          onChange={(e) => setTahun(parseInt(e.target.value))}
          className="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
        >
          {[2023, 2024, 2025, 2026].map((y) => (
            <option key={y} value={y}>{y}</option>
          ))}
        </select>
      </div>

      <div className="border-b border-gray-200 dark:border-gray-800">
        <nav className="flex gap-4">
          <button
            onClick={() => setActiveTab('summary')}
            className={`px-4 py-2 border-b-2 font-medium text-sm ${
              activeTab === 'summary'
                ? 'border-primary-600 text-primary-600'
                : 'border-transparent text-gray-500 hover:text-gray-700'
            }`}
          >
            Summary
          </button>
          <button
            onClick={() => setActiveTab('chart')}
            className={`px-4 py-2 border-b-2 font-medium text-sm ${
              activeTab === 'chart'
                ? 'border-primary-600 text-primary-600'
                : 'border-transparent text-gray-500 hover:text-gray-700'
            }`}
          >
            Grafik Tahunan
          </button>
          <button
            onClick={() => setActiveTab('detail')}
            className={`px-4 py-2 border-b-2 font-medium text-sm ${
              activeTab === 'detail'
                ? 'border-primary-600 text-primary-600'
                : 'border-transparent text-gray-500 hover:text-gray-700'
            }`}
          >
            Detail Bulanan
          </button>
        </nav>
      </div>

      {activeTab === 'summary' && (
        <div className="space-y-4">
          {summary && (
            <>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm text-gray-500 dark:text-gray-400">Total Pemasukan</p>
                      <p className="text-2xl font-bold text-green-600 mt-1">{formatCurrency(summary.total_pemasukan || 0)}</p>
                    </div>
                    <div className="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center">
                      <TrendingUp className="w-6 h-6 text-green-600" />
                    </div>
                  </div>
                </div>
                <div className="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm text-gray-500 dark:text-gray-400">Total Pengeluaran</p>
                      <p className="text-2xl font-bold text-red-600 mt-1">{formatCurrency(summary.total_pengeluaran || 0)}</p>
                    </div>
                    <div className="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-xl flex items-center justify-center">
                      <TrendingDown className="w-6 h-6 text-red-600" />
                    </div>
                  </div>
                </div>
                <div className="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm text-gray-500 dark:text-gray-400">Saldo</p>
                      <p className={`text-2xl font-bold mt-1 ${(summary.saldo || 0) >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                        {formatCurrency(summary.saldo || 0)}
                      </p>
                    </div>
                    <div className="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                      <Wallet className="w-6 h-6 text-blue-600" />
                    </div>
                  </div>
                </div>
              </div>

              <div className="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                  Detail Pemasukan & Pengeluaran {monthNames[bulan - 1]} {tahun}
                </h3>
                <div className="space-y-3">
                  {summary.pemasukan_detail?.length > 0 ? (
                    summary.pemasukan_detail.map((item, idx) => (
                      <div key={idx} className="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                          <p className="font-medium text-gray-900 dark:text-white">{item.keterangan || 'Pemasukan'}</p>
                          <p className="text-xs text-gray-500 dark:text-gray-400">{item.tanggal}</p>
                        </div>
                        <p className="font-medium text-green-600">+{formatCurrency(item.nominal)}</p>
                      </div>
                    ))
                  ) : (
                    <p className="text-center text-gray-500 py-4">Tidak ada pemasukan</p>
                  )}
                  {summary.pengeluaran_detail?.length > 0 ? (
                    summary.pengeluaran_detail.map((item, idx) => (
                      <div key={idx} className="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                          <p className="font-medium text-gray-900 dark:text-white">{item.keterangan || item.nama_pengeluaran}</p>
                          <p className="text-xs text-gray-500 dark:text-gray-400">{item.tanggal}</p>
                        </div>
                        <p className="font-medium text-red-600">-{formatCurrency(item.nominal)}</p>
                      </div>
                    ))
                  ) : (
                    <p className="text-center text-gray-500 py-4">Tidak ada pengeluaran</p>
                  )}
                </div>
              </div>
            </>
          )}
        </div>
      )}

      {activeTab === 'chart' && (
        <div className="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800">
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Pemasukan & Pengeluaran {chart?.tahun || tahun}
          </h3>
          <ResponsiveContainer width="100%" height={400}>
            <BarChart data={months.map((m, i) => ({
              nama: m,
              pemasukan: chart?.pemasukan?.[i] || 0,
              pengeluaran: chart?.pengeluaran?.[i] || 0,
            }))}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="nama" />
              <YAxis />
              <Tooltip formatter={(value) => formatCurrency(value)} />
              <Legend />
              <Bar dataKey="pemasukan" fill="#22c55e" name="Pemasukan" />
              <Bar dataKey="pengeluaran" fill="#ef4444" name="Pengeluaran" />
            </BarChart>
          </ResponsiveContainer>
        </div>
      )}

      {activeTab === 'detail' && (
        <div className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800">
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pembayaran Iuran</h3>
              <div className="space-y-2">
                {detail?.pembayaran?.length > 0 ? (
                  detail.pembayaran.map((item, idx) => (
                    <div key={idx} className="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                      <div>
                        <p className="font-medium text-gray-900 dark:text-white">{item.keterangan || item.payment_type}</p>
                        <p className="text-xs text-gray-500 dark:text-gray-400">{item.tanggal}</p>
                      </div>
                      <p className="font-medium text-green-600">+{formatCurrency(item.nominal)}</p>
                    </div>
                  ))
                ) : (
                  <p className="text-center text-gray-500 py-4">Tidak ada pembayaran</p>
                )}
              </div>
            </div>

            <div className="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800">
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pengeluaran</h3>
              <div className="space-y-2">
                {detail?.pengeluaran?.length > 0 ? (
                  detail.pengeluaran.map((item, idx) => (
                    <div key={idx} className="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                      <div>
                        <p className="font-medium text-gray-900 dark:text-white">{item.nama_pengeluaran}</p>
                        <p className="text-xs text-gray-500 dark:text-gray-400">{item.tanggal}</p>
                      </div>
                      <p className="font-medium text-red-600">-{formatCurrency(item.nominal)}</p>
                    </div>
                  ))
                ) : (
                  <p className="text-center text-gray-500 py-4">Tidak ada pengeluaran</p>
                )}
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}