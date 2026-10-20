import { mkdirSync, readFileSync, writeFileSync, renameSync, existsSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
export type Entry = { slug: string; title: string; description: string; content: string; url: string };
export type Collection = 'aplikasi';
export const collections: Collection[] = ['aplikasi'];
export type Content = Record<Collection, Entry[]>;
export function createStore(file = resolve('data/content.json')) {
  mkdirSync(dirname(file), { recursive: true });
  if (!existsSync(file)) writeFileSync(file, JSON.stringify({ aplikasi: [] }));
  const read = (): Content => {
    const data = JSON.parse(readFileSync(file, 'utf8'));
    return { aplikasi: data.aplikasi ?? [] };
  };
  const save = (data: Content) => {
    const temp = `${file}.tmp`;
    writeFileSync(temp, JSON.stringify(data, null, 2));
    renameSync(temp, file);
  };
  return { read, save };
}
export function validateEntry(value: unknown): Entry | null {
  if (!value || typeof value !== 'object') return null;
  const v = value as Record<string, unknown>;
  if (typeof v.slug !== 'string' || !/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(v.slug) || v.slug.length > 120) return null;
  if (typeof v.title !== 'string' || !v.title.trim() || v.title.length > 200) return null;
  if (typeof v.description !== 'string' || v.description.length > 2000 || typeof v.content !== 'string' || v.content.length > 100000) return null;
  if (typeof v.url !== 'string') return null;
  if (v.url) { try { if (!['https:', 'http:'].includes(new URL(v.url).protocol)) return null; } catch { return null; } }
  return { slug: v.slug, title: v.title.trim(), description: v.description, content: v.content, url: v.url };
}
