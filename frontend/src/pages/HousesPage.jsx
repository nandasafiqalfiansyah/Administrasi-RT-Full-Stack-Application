import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { Home, Plus, Search } from 'lucide-react';
import api from '../lib/api';

export default function HousesPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['houses'],
    queryFn: async () => {
      const res = await api.get('/houses');
      return res.data;
    },
  });

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Rumah</h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data rumah RT</p>
        </div>
        <button className="flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">
          <Plus className="w-4 h-4" /> Tambah
        </button>
      </div>

      <div className="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-8 text-center">
        <Home className="w-12 h-12 text-gray-400 mx-auto mb-3" />
        <p className="text-gray-500">Fitur manajemen rumah akan segera tersedia</p>
      </div>
    </div>
  );
}