import { z } from "zod";

export const createSchema = z.object({
    duesName: z.coerce
        .string({
            required_error: "Nama Kategori tidak boleh kosong",
            message: "Nama Kategori tidak boleh kosong"
        })
        .min(1)
        .max(20),
    duesAmount: z.coerce
        .string({
            required_error: "Jumlah iuran tidak boleh kosong",
        })
        .min(1),
});

export type CreateSchema = z.infer<typeof createSchema>;
