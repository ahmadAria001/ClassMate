import { z } from 'zod'

export const createSchema = z.object({
    description: z.coerce.string().min(1, 'Keterangan tidak boleh kosong!'),
    request_by: z.coerce.number().min(1, 'Pemohon todak boleh kosong!')
})

export type CreateSchema = z.infer<typeof createSchema>;


