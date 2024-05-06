import { z } from 'zod'

export const updateSchema = z.object({
    id: z.coerce.number().min(1),
    number: z.coerce.number().min(1),
    leader_id: z.coerce.number().min(1)
})

export type UpdateSchema = z.infer<typeof updateSchema>;

