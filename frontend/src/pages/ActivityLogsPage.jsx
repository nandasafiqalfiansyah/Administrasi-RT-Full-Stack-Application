import React from 'react';
import { Activity } from 'lucide-react';

export default function ActivityLogsPage() {
  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Log Aktivitas</h1>
      <div className="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-8 text-center">
        <Activity className="w-12 h-12 text-gray-400 mx-auto mb-3" />
        <p className="text-gray-500">Fitur log aktivitas akan segera tersedia</p>
      </div>
    </div>
  );
}