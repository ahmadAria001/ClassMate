import { z } from "zod";

export const createSchema = z.object({
    nik: z.string().min(1, { message: "NIK Tidak Boleh Kosong" }).max(16),
    fullName: z.string().min(1, { message: "Nama Lengkap Tidak Boleh Kosong" }),
    birthplace: z
        .string()
        .min(1, { message: "Tempat Lahir Tidak Boleh Kosong" }),
    birthdate: z
        .string()
        .min(1, { message: "Tanggal Lahir Tidak Boleh Kosong" })
        .transform((str) => {
            const formatted = str.replace("-", "/");
            return new Date(formatted).getSeconds();
        }),
    nkk: z.string().min(1, { message: "NKK Tidak Boleh Kosong" }),
    residentstatus: z
        .string()
        .min(1, { message: "Status Penduduk Tidak Boleh Kosong" }),
    rt_id: z.number().min(1, { message: "RT Tidak Boleh Kosong" }),
    address: z.string().min(1, { message: "Alamat Tidak Boleh Kosong" }),
    status: z.string().min(1, { message: "Status Tidak Boleh Kosong" }),
    phone: z
        .string()
        .min(1, { message: "Nomor HP Tidak Boleh Kosong" })
        .transform((str) => {
            if (!str) throw new Error("Nomor HP Tidak Boleh Kosong");
            return str;
        }),
    religion: z.string().min(1, { message: "Agama Tidak Boleh Kosong" }),
    job: z.string().min(1, { message: "Pekerjaan Tidak Boleh Kosong" }),
    isFamilyHead: z.boolean(),
});

export type CreateSchema = z.infer<typeof createSchema>;
