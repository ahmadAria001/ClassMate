import { z } from 'zod'

export const createSchema = z.object({
    name: z.coerce.string().min(1, 'Nama Kegiatan tidak boleh kosong!'),
    startDate: z.coerce.string().min(1, 'Tanggal Mulai tidak boleh kosong!').transform((str) => Date.parse(str)),
    endDate: z.coerce.string().min(1, 'Tanggal Berakhir tidak boleh kosong!').transform((str) => Date.parse(str)),
    location: z.coerce.string().min(1, 'Lokasi tidak boleh kosong!'),
    description: z.coerce.string().min(1, 'Description tidak boleh kosong!')
}).superRefine(({ name, startDate, endDate, location, description }, ctx) => {

    if (endDate < startDate) {
        ctx.addIssue({
            code: 'custom',
            message: 'Tanggal Berakhir tidak boleh kurang dari Tanggal Dimulai',
            path: ['endDate']
        })
    }

    if (startDate > endDate) {
        ctx.addIssue({
            code: 'custom',
            message: 'Tanggal Dimulai tidak boleh Lebih dari Tanggal Berakhir',
            path: ['startDate']
        })
    }

})

export type CreateSchema = z.infer<typeof createSchema>;
