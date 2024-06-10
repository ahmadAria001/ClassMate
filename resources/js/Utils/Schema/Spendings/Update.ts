import { z } from "zod";

export const updateSchema = z.object({
    category: z.coerce.string().min(1, "Category tidak boleh kosnog"),
    desc: z.coerce.string().min(1, "deskripsi tidak boleh kosong"),
    amount: z.coerce.number().min(1, "Minimal jumlah harus lebih dari 0"),
});

export type UpdateSchema = z.infer<typeof updateSchema>;
