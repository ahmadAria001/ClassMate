import { z } from "zod";

export const createSchema = z.object({
    full_name: z.string().nonempty("Nama Lengkap harus diisi"),
    jumlah_tanggungan: z.enum(['1', '2', '3', '4', '5'], {
        message: "Tanggungan tidak boleh kosong",
    }),
    pendapatan_bulanan: z.enum(['1', '2', '3', '4', '5'], {
        message: "Pendapatan bulanan tidak boleh kosong",
    }),
    pengeluaran_bulanan: z.enum(['1', '2', '3', '4', '5'], {
        message: "Pengeluaran bulanan tidak boleh kosong ",
    }),
    // masih belum ambil data ril dari database
    status_pekerjaan: z.enum(['3', '4', '5'], {
        message: "Status pekerjaan tidak boleh kosong",
    }),
    status_tempat_tinggal: z.enum(['3', '4', '5'], {
        message: "Status tempat tinggal tidak boleh kosong",
    }),
});

export type CreateSchema = z.infer<typeof createSchema>;
